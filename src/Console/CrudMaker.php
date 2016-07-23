<?php

namespace Yab\CrudMaker\Console;

use Config;
use Exception;
use Illuminate\Console\Command;
use Yab\CrudMaker\Services\ConfigService;
use Yab\CrudMaker\Services\CrudService;
use Yab\CrudMaker\Generators\CrudGenerator;
use Yab\CrudMaker\Generators\DatabaseGenerator;
use Yab\CrudMaker\Services\AppService;
use Yab\CrudMaker\Services\ValidatorService;

class CrudMaker extends Command
{
    /**
     * Column Types.
     *
     * @var array
     */
    public $columnTypes = [
        'bigIncrements',
        'increments',
        'bigInteger',
        'binary',
        'boolean',
        'char',
        'date',
        'dateTime',
        'decimal',
        'double',
        'enum',
        'float',
        'integer',
        'ipAddress',
        'json',
        'jsonb',
        'longText',
        'macAddress',
        'mediumInteger',
        'mediumText',
        'morphs',
        'smallInteger',
        'string',
        'string',
        'text',
        'time',
        'tinyInteger',
        'timestamp',
        'uuid',
    ];

    /**
     * The console command name.
     *
     * @var string
     */
    protected $signature = 'crudmaker:new {table}
        {--api : Creates an API Controller and Routes}
        {--apiOnly : Creates only the API Controller and Routes}
        {--ui= : Select one of bootstrap|semantic for the UI}
        {--serviceOnly : Does not generate a Controller or Routes}
        {--withFacade : Creates a facade that can be bound in your app to access the CRUD service}
        {--migration : Generates a migration file}
        {--schema= : Basic schema support ie: id,increments,name:string,parent_id:integer}
        {--relationships= : Define the relationship ie: hasOne|App\Comment|comment,hasOne|App\Rating|rating or relation|class|column (without the _id)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate a magical CRUD for a table with options for: Migration, API, UI, Schema and even Relationships';

    /**
     * The app service
     *
     * @var AppService
     */
    protected $appService;

    /**
     * The Crud service
     *
     * @var CrudService
     */
    protected $crudService;

    /**
     * The Crud generator
     *
     * @var CrudGenerator
     */
    protected $crudGenerator;

    /**
     * The Config service
     *
     * @var ConfigService
     */
    protected $configService;

    /**
     * The validator service
     *
     * @var ValidatorService
     */
    protected $validator;

    /**
     * CrudMaker Constructor
     *
     * @param AppService       $appService
     * @param CrudService      $crudService
     * @param crudGenerator    $crudGenerator
     * @param ConfigService    $configService
     * @param ValidatorService $validator
     */
    public function __construct(
        AppService $appService,
        CrudService $crudService,
        CrudGenerator $crudGenerator,
        ConfigService $configService,
        ValidatorService $validator
    ) {
        parent::__construct();

        $this->appService = $appService;
        $this->crudService = $crudService;
        $this->crudGenerator = $crudGenerator;
        $this->configService = $configService;
        $this->validator = $validator;
    }

    /**
     * Generate a CRUD stack.
     *
     * @return mixed
     */
    public function handle()
    {
        $section = '';
        $splitTable = [];

        $appPath = app()->path();
        $basePath = app()->basePath();
        $appNamespace = $this->appService->getAppNamespace();
        $framework = ucfirst('Laravel');

        if (stristr(get_class(app()), 'Lumen')) {
            $framework = ucfirst('lumen');
        }

        $table = ucfirst(str_singular($this->argument('table')));

        $this->validator->validateSchema($this);
        $this->validator->validateOptions($this);

        $options = [
            'api'                => $this->option('api'),
            'apiOnly'            => $this->option('apiOnly'),
            'ui'                 => $this->option('ui'),
            'serviceOnly'        => $this->option('serviceOnly'),
            'withFacade'         => $this->option('withFacade'),
            'migration'          => $this->option('migration'),
            'schema'             => $this->option('schema'),
            'relationships'      => $this->option('relationships'),
        ];

        $config = $this->configService->basicConfig(
            $framework,
            $appPath,
            $basePath,
            $appNamespace,
            $table,
            $options
        );

        if ($this->option('ui')) {
            $config[$this->option('ui')] = true;
        }

        $config['schema'] = $this->option('schema');
        $config['relationships'] = $this->option('relationships');
        $config['template_source'] = $this->configService->getTemplateConfig($framework, $basePath);

        if (stristr($table, '_')) {
            $splitTable = explode('_', $table);
            $table = $splitTable[1];
            $section = $splitTable[0];
            $config = $this->configService->configASectionedCRUD($config, $section, $table, $splitTable);
        } else {
            $config = array_merge($config, app('config')->get('crudmaker.single', []));
            $config = $this->configService->setConfig($config, $section, $table);
        }

        $this->createCRUD($config, $section, $table, $splitTable);

        $this->info("\nYou may wish to add this as your testing database:\n");
        $this->comment("'testing' => [ 'driver' => 'sqlite', 'database' => ':memory:', 'prefix' => '' ],");
        $this->info("\n".'You now have a working CRUD for '.$table."\n");
    }

    /**
     * Create a CRUD.
     *
     * @param array  $config
     * @param string $section
     * @param string $table
     * @param array  $splitTable
     *
     * @return void
     */
    public function createCRUD($config, $section, $table, $splitTable)
    {
        $bar = $this->output->createProgressBar(7);

        try {
            $this->crudService->generateCore($config, $bar);
            $this->crudService->generateAppBased($config, $bar);

            $this->crudGenerator->createTests(
                $config,
                $this->option('serviceOnly'),
                $this->option('apiOnly'),
                $this->option('api')
            );
            $bar->advance();

            $this->crudGenerator->createFactory($config);
            $bar->advance();

            $this->crudService->generateAPI($config, $bar);
            $bar->advance();

            $this->crudService->generateDB($config, $bar, $section, $table, $splitTable, $this);
            $bar->finish();

            $this->crudReport($table);
        } catch (Exception $e) {
            dd($e);
            throw new Exception('Unable to generate your CRUD: '.$e->getMessage(), 1);
        }
    }

    /**
     * Generate a CRUD report.
     *
     * @param string $table
     *
     * @return void
     */
    private function crudReport($table)
    {
        $this->line("\n");
        $this->line('Built repository...');
        $this->line('Built request...');
        $this->line('Built service...');

        if (!$this->option('serviceOnly') && !$this->option('apiOnly')) {
            $this->line('Built controller...');
            $this->line('Built views...');
            $this->line('Built routes...');
        }

        if ($this->option('withFacade')) {
            $this->line('Built facade...');
        }

        $this->line('Built tests...');
        $this->line('Added '.$table.' to database/factories/ModelFactory...');

        if ($this->option('api') || $this->option('apiOnly')) {
            $this->line('Built api...');
            $this->comment("\nAdd the following to your app/Providers/RouteServiceProvider.php: \n");
            $this->info("require app_path('Http/api-routes.php'); \n");
        }

        if ($this->option('migration')) {
            $this->line('Built migration...');
            if ($this->option('schema')) {
                $this->line('Built schema...');
            }
        } else {
            $this->info("\nYou will want to create a migration in order to get the $table tests to work correctly.\n");
        }
    }
}
