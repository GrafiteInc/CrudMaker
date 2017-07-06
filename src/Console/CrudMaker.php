<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/7/3
 * Time: 15:37
 */
namespace Louis\CrudMaker\Console;

use Config;
use Exception;
use Illuminate\Console\Command;
use Louis\CrudMaker\Services\CrudService;
use Louis\CrudMaker\Services\ConfigService;

class CrudMaker extends Command
{
    protected $crudService;

    protected $configService;
    /**
     * The console command name.
     *
     * @var string
     */
    protected $signature = 'crudmaker:crud {table}
        {--api : Creates an API Controller and Routes}
        {--apiOnly : Creates only the API Controller and Routes}
        {--withoutViews : Prevent the generating of views}
        {--serviceOnly : Does not generate a Controller or Routes}
        {--migration : Generates a migration file}
        {--schema= : Basic schema support ie: id,increments,name:string,parent_id:integer}
        {--relationships= : Define the relationship ie: hasOne|App\Comment|comment,hasOne|App\Rating|rating or relation|class|column (without the _id)}';
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate a magical CRUD for a table with options for: Migration, API, Schema and even Relationships';

    public function __construct(CrudService $crudService, ConfigService $configService){
        parent::__construct();
        $this->crudService = $crudService;
        $this->configService = $configService;
    }

    public function handle()
    {
        $table = strtolower($this->argument('table'));
        $options = [
            'api' => $this->option('api'),
            'apiOnly' => $this->option('apiOnly'),
            'serviceOnly' => $this->option('serviceOnly'),
            'migration' => $this->option('migration'),
            'schema' => $this->option('schema'),
            'relationships' => $this->option('relationships'),
        ];
        $config = $this->configService->getConfig($table, $options);
//        if (stristr($table, '_')) {
//            $splitTable = explode('_', $table);
//            unset($splitTable[0]);
//            $table = implode("_", $splitTable);
//        }


        $this->createCRUD($config);
    }

    public function createCRUD($config){
        $bar = $this->output->createProgressBar(8);
        try {
            $this->crudService->setConfig($config, $bar);

            $this->crudService->createModel();
            $this->line(" Model created");
            $this->crudService->createRequest();
            $this->line(" Request created");
            $this->crudService->createService($config, $bar);
            $this->line(" Service created");
            if (!$config['options-serviceOnly']) {
                if (!$config['options-apiOnly']) {
                    $this->crudService->createRoute($config, $bar);
                    $this->line(" Route created");
                    $this->crudService->createController($config, $bar);
                    $this->line(" Controller created");
                    $this->crudService->createViews($config, $bar);
                    $this->line(" Views created");
                }
                if ($config['options-api']) {
                    $this->crudService->createApiController();
                    $this->line(" Api Controller created");
                    $this->crudService->createApiRoute();
                    $this->line(" Api Route created");
                }
            }
        }catch (\Exception $e){
            throw new Exception('Unable to generate your CRUD: ('.$e->getFile().':'.$e->getLine().') '.$e->getMessage(), 1);
        }

    }

}