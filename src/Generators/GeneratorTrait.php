<?php

namespace Yab\CrudMaker\Generators;

trait GeneratorTrait
{
    /**
     * Generate core elements.
     *
     * @param \Yab\CrudMaker\Generators\CrudGenerator        $crudGenerator
     * @param array                                         $config
     * @param \Symfony\Component\Console\Helper\ProgressBar $bar
     *
     * @return void
     */
    public function generateCore($crudGenerator, $config, $bar)
    {
        $crudGenerator->createRepository($config);
        $crudGenerator->createService($config);

        if ($config['framework'] === 'laravel') {
            $crudGenerator->createRequest($config);
        }

        $bar->advance();
    }

    /**
     * Generate app based elements.
     *
     * @param \Yab\CrudMaker\Generators\CrudGenerator        $crudGenerator
     * @param array                                         $config
     * @param \Symfony\Component\Console\Helper\ProgressBar $bar
     *
     * @return void
     */
    public function generateAppBased($crudGenerator, $config, $bar)
    {
        if (!$this->option('serviceOnly') && !$this->option('apiOnly')) {
            $crudGenerator->createController($config);
            $crudGenerator->createViews($config);
            $crudGenerator->createRoutes($config);

            if ($this->option('withFacade')) {
                $crudGenerator->createFacade($config);
            }
        }
        $bar->advance();
    }

    /**
     * Generate db elements.
     *
     * @param \Yab\CrudMaker\Generators\DatabaseGenerator    $dbGenerator
     * @param \Symfony\Component\Console\Helper\ProgressBar $bar
     * @param string                                        $section
     * @param string                                        $table
     * @param array                                         $splitTable
     *
     * @return void
     */
    public function generateDB($dbGenerator, $config, $bar, $section, $table, $splitTable)
    {
        if ($this->option('migration')) {
            $dbGenerator->createMigration($config, $section, $table, $splitTable, $this);
            if ($this->option('schema')) {
                $dbGenerator->createSchema($config, $section, $table, $splitTable, $this->option('schema'));
            }
        }
        $bar->advance();
    }

    /**
     * Generate api elements.
     *
     * @param \Yab\CrudMaker\Generators\CrudGenerator        $crudGenerator
     * @param array                                         $config
     * @param \Symfony\Component\Console\Helper\ProgressBar $bar
     *
     * @return void
     */
    public function generateAPI($crudGenerator, $config, $bar)
    {
        if ($this->option('api') || $this->option('apiOnly')) {
            $crudGenerator->createApi($config);
        }
        $bar->advance();
    }
}
