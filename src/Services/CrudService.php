<?php

namespace Yab\CrudMaker\Services;

use Yab\CrudMaker\Generators\CrudGenerator;
use Yab\CrudMaker\Generators\DatabaseGenerator;

class CrudService
{
    protected $crudGenerator;
    protected $dbGenerator;

    public function __construct(
        CrudGenerator $crudGenerator,
        DatabaseGenerator $dbGenerator
    ) {
        $this->crudGenerator = $crudGenerator;
        $this->dbGenerator = $dbGenerator;
    }

    /**
     * Generate core elements.
     *
     * @param array                                         $config
     * @param \Symfony\Component\Console\Helper\ProgressBar $bar
     */
    public function generateCore($config, $bar)
    {
        $this->crudGenerator->createModel($config);
        $this->crudGenerator->createService($config);

        if (strtolower($config['framework']) === 'laravel') {
            $this->crudGenerator->createRequest($config);
        }

        $bar->advance();
    }

    /**
     * Generate app based elements.
     *
     * @param array                                         $config
     * @param \Symfony\Component\Console\Helper\ProgressBar $bar
     */
    public function generateAppBased($config, $bar)
    {
        if (!$config['options-serviceOnly'] && !$config['options-apiOnly']) {
            $this->crudGenerator->createController($config);
            $this->crudGenerator->createViews($config);
            $this->crudGenerator->createRoutes($config);

            if ($config['options-withFacade']) {
                $this->crudGenerator->createFacade($config);
            }
        }
        $bar->advance();
    }

    /**
     * Generate db elements.
     *
     * @param \Symfony\Component\Console\Helper\ProgressBar $bar
     * @param string                                        $section
     * @param string                                        $table
     * @param array                                         $splitTable
     * @param \Yab\CrudMaker\Console\CrudMaker              $command
     */
    public function generateDB($config, $bar, $section, $table, $splitTable, $command)
    {
        if ($config['options-migration']) {
            $this->dbGenerator->createMigration(
                $config,
                $section,
                $table,
                $splitTable,
                $command
            );
            if ($config['options-schema']) {
                $this->dbGenerator->createSchema(
                    $config,
                    $section,
                    $table,
                    $splitTable,
                    $config['options-schema']
                );
            }
        }
        $bar->advance();
    }

    /**
     * Generate api elements.
     *
     * @param array                                         $config
     * @param \Symfony\Component\Console\Helper\ProgressBar $bar
     */
    public function generateAPI($config, $bar)
    {
        if ($config['options-api'] || $config['options-apiOnly']) {
            $this->crudGenerator->createApi($config);
        }
        $bar->advance();
    }

    /**
     * Generates a service provider.
     *
     * @param  array $config
     */
    public function generatePackageServiceProvider($config)
    {
        $this->crudGenerator->generatePackageServiceProvider($config);
    }

    /**
     * Corrects the namespace for the view.
     *
     * @param  array $config
     */
    public function correctViewNamespace($config)
    {
        $controllerFile = $config['_path_controller_'].'/'.$config['_ucCamel_casePlural_'].'Controller.php';

        $controller = file_get_contents($controllerFile);

        $controller = str_replace("view('".$config['_sectionPrefix_'].$config['_lower_casePlural_'].".", "view('".$config['_sectionPrefix_'].$config['_lower_casePlural_']."::", $controller);

        file_put_contents($controllerFile, $controller);
    }
}
