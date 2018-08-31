<?php

namespace Grafite\CrudMaker\Console;

use Config;
use Exception;
use Illuminate\Console\Command;
use Grafite\CrudMaker\Generators\CrudGenerator;
use Grafite\CrudMaker\Services\AppService;
use Grafite\CrudMaker\Services\ConfigService;
use Grafite\CrudMaker\Services\CrudService;
use Grafite\CrudMaker\Services\ValidatorService;

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
        {--withoutViews : Prevent the generating of views}
        {--serviceOnly : Does not generate a Controller or Routes}
        {--withBaseService : Creates service as an extension of a BaseService class}
        {--withFacade : Creates a facade that can be bound in your app to access the CRUD service}
        {--migration : Generates a migration file}
        {--asPackage= : Generate the CRUD as a package by setting a directory}
        {--schema= : Basic schema support ie: id,increments,name:string,parent_id:integer}
        {--relationships= : Define the relationship ie: hasOne|App\Comment|comment,hasOne|App\Rating|rating or relation|class|column (without the _id)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate a magical CRUD for a table with options for: Migration, API, UI, Schema and even Relationships';

    /**
     * The app service.
     *
     * @var AppService
     */
    protected $appService;

    /**
     * The Crud service.
     *
     * @var CrudService
     */
    protected $crudService;

    /**
     * The Crud generator.
     *
     * @var CrudGenerator
     */
    protected $crudGenerator;

    /**
     * The Config service.
     *
     * @var ConfigService
     */
    protected $configService;

    /**
     * The validator service.
     *
     * @var ValidatorService
     */
    protected $validator;

    /**
     * CrudMaker Constructor.
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
            'api' => $this->option('api'),
            'apiOnly' => $this->option('apiOnly'),
            'ui' => $this->option('ui'),
            'serviceOnly' => $this->option('serviceOnly'),
            'withFacade' => $this->option('withFacade'),
            'withBaseService' => $this->option('withBaseService'),
            'migration' => $this->option('migration'),
            'schema' => $this->option('schema'),
            'asPackage' => $this->option('asPackage'),
            'relationships' => $this->option('relationships'),
        ];

        if ($this->option('asPackage')) {
            $newPath = base_path($this->option('asPackage').'/'.str_plural($table));
            if (!is_dir($newPath)) {
                mkdir($newPath, 755, true);
            }
            $appPath = $newPath;
            $basePath = $newPath;
            $appNamespace = ucfirst($this->option('asPackage'));
        }

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
        $config['template_source'] = $this->configService->getTemplateConfig($framework);

        if (stristr($table, '_')) {
            $splitTable = explode('_', $table);
            $table = $splitTable[1];
            $section = $splitTable[0];
            $config = $this->configService->configASectionedCRUD($config, $section, $table, $splitTable);
        } else {
            $config = array_merge($config, app('config')->get('crudmaker.single', []));
            $config = $this->configService->setConfig($config, $section, $table);
        }

        if ($this->option('asPackage')) {
            $moduleDirectory = base_path($this->option('asPackage').'/'.str_plural($table));
            $config = array_merge($config, [
                '_path_package_' => $moduleDirectory,
                '_path_facade_' => $moduleDirectory.'/Facades',
                '_path_service_' => $moduleDirectory.'/Services',
                '_path_model_' => $moduleDirectory.'/Models',
                '_path_model_' => $moduleDirectory.'/Models',
                '_path_controller_' => $moduleDirectory.'/Controllers',
                '_path_views_' => $moduleDirectory.'/Views',
                '_path_tests_' => $moduleDirectory.'/Tests',
                '_path_request_' => $moduleDirectory.'/Requests',
                '_path_routes_' => $moduleDirectory.'/Routes/web.php',
                '_namespace_services_' => $appNamespace.'\\'.ucfirst(str_plural($table)).'\Services',
                '_namespace_facade_' => $appNamespace.'\\'.ucfirst(str_plural($table)).'\Facades',
                '_namespace_model_' => $appNamespace.'\\'.ucfirst(str_plural($table)).'\Models',
                '_namespace_controller_' => $appNamespace.'\\'.ucfirst(str_plural($table)).'\Controllers',
                '_namespace_request_' => $appNamespace.'\\'.ucfirst(str_plural($table)).'\Requests',
                '_namespace_package_' => $appNamespace.'\\'.ucfirst(str_plural($table)),
            ]);

            if (! is_dir($moduleDirectory.'/Routes')) {
                mkdir($moduleDirectory.'/Routes');
            }
        }

        $this->createCRUD($config, $section, $table, $splitTable);

        if ($this->option('asPackage')) {
            $this->createPackageServiceProvider($config);
            $this->crudService->correctViewNamespace($config);
        }

        $this->info("\nYou may wish to add this as your testing database:\n");
        $this->comment("'testing' => [ 'driver' => 'sqlite', 'database' => ':memory:', 'prefix' => '' ],");
        $this->info("\n".'You now have a working CRUD for '.$table."\n");
    }

    /**
     * Generate a service provider for the new module.
     *
     * @param  array $config
     */
    public function createPackageServiceProvider($config)
    {
        $this->crudService->generatePackageServiceProvider($config);
    }

    /**
     * Create a CRUD.
     *
     * @param array  $config
     * @param string $section
     * @param string $table
     * @param array  $splitTable
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
            throw new Exception('Unable to generate your CRUD: ('.$e->getFile().':'.$e->getLine().') '.$e->getMessage(), 1);
        }
    }

    /**
     * Generate a CRUD report.
     *
     * @param string $table
     */
    private function crudReport($table)
    {
        $this->line("\n");
        $this->line('Built model...');
        $this->line('Built request...');
        $this->line('Built service...');

        if (!$this->option('serviceOnly') && !$this->option('apiOnly')) {
            $this->line('Built controller...');
            if (!$this->option('withoutViews')) {
                $this->line('Built views...');
            }
            $this->line('Built routes...');
        }

        if ($this->option('withFacade')) {
            $this->line('Built facade...');
        }

        $this->line('Built tests...');
        $this->line('Built factory...');

        if ($this->option('api') || $this->option('apiOnly')) {
            $this->line('Built api...');
            $this->comment("\nAdd the following to your app/Providers/RouteServiceProvider.php: \n");
            $this->info("require base_path('routes/api.php'); \n");
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
