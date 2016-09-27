<?php

namespace Yab\CrudMaker\Generators;

use Illuminate\Filesystem\Filesystem;
use Yab\CrudMaker\Services\ModelService;
use Yab\CrudMaker\Services\TableService;
use Yab\CrudMaker\Services\TestService;

/**
 * Generate the CRUD.
 */
class CrudGenerator
{
    protected $filesystem;
    protected $tableService;
    protected $testService;
    protected $modelService;

    public function __construct()
    {
        $this->filesystem = new Filesystem();
        $this->tableService = new TableService();
        $this->testService = new TestService();
        $this->modelService = new ModelService();
    }

    /**
     * Create the controller.
     *
     * @param array $config
     *
     * @return bool
     */
    public function createController($config)
    {
        if (!is_dir($config['_path_controller_'])) {
            mkdir($config['_path_controller_'], 0777, true);
        }

        $request = $this->filesystem->get($config['template_source'].'/Controller.txt');

        foreach ($config as $key => $value) {
            $request = str_replace($key, $value, $request);
        }

        $request = $this->filesystem->put($config['_path_controller_'].'/'.$config['_ucCamel_casePlural_'].'Controller.php', $request);

        return $request;
    }

    /**
     * Create the model.
     *
     * @param array $config
     *
     * @return bool
     */
    public function createModel($config)
    {
        $repoParts = [
            '_path_model_',
        ];

        foreach ($repoParts as $repoPart) {
            if (!is_dir($config[$repoPart])) {
                mkdir($config[$repoPart], 0777, true);
            }
        }

        $model = $this->filesystem->get($config['template_source'].'/Model.txt');
        $model = $this->configTheModel($config, $model);

        foreach ($config as $key => $value) {
            $model = str_replace($key, $value, $model);
        }

        $model = $this->filesystem->put($config['_path_model_'].'/'.$config['_camel_case_'].'.php', $model);

        return $model;
    }

    /**
     * Configure the model.
     *
     * @param array  $config
     * @param string $model
     *
     * @return string
     */
    public function configTheModel($config, $model)
    {
        if (!empty($config['schema'])) {
            $model = str_replace('// _camel_case_ table data', $this->tableService->prepareTableDefinition($config['schema']), $model);
        }

        if (!empty($config['relationships'])) {
            $relationships = [];

            foreach (explode(',', $config['relationships']) as $relationshipExpression) {
                $relationships[] = explode('|', $relationshipExpression);
            }

            $model = str_replace('// _camel_case_ relationships', $this->modelService->prepareModelRelationships($relationships), $model);
        }

        return $model;
    }

    /**
     * Create the request.
     *
     * @param array $config
     *
     * @return bool
     */
    public function createRequest($config)
    {
        if (!is_dir($config['_path_request_'])) {
            mkdir($config['_path_request_'], 0777, true);
        }

        $request = $this->filesystem->get($config['template_source'].'/Request.txt');

        foreach ($config as $key => $value) {
            $request = str_replace($key, $value, $request);
        }

        $request = $this->filesystem->put($config['_path_request_'].'/'.$config['_camel_case_'].'Request.php', $request);

        return $request;
    }

    /**
     * Create the service.
     *
     * @param array $config
     *
     * @return bool
     */
    public function createService($config)
    {
        if (!is_dir($config['_path_service_'])) {
            mkdir($config['_path_service_'], 0777, true);
        }

        $request = $this->filesystem->get($config['template_source'].'/Service.txt');

        foreach ($config as $key => $value) {
            $request = str_replace($key, $value, $request);
        }

        $request = $this->filesystem->put($config['_path_service_'].'/'.$config['_camel_case_'].'Service.php', $request);

        return $request;
    }

    /**
     * Create the routes.
     *
     * @param array $config
     *
     * @return bool
     */
    public function createRoutes($config)
    {
        $routesMaster = $config['_path_routes_'];

        if (!empty($config['routes_prefix'])) {
            $this->filesystem->append($routesMaster, $config['routes_prefix']);
        }

        $routes = $this->filesystem->get($config['template_source'].'/Routes.txt');

        foreach ($config as $key => $value) {
            $routes = str_replace($key, $value, $routes);
        }

        $this->filesystem->append($routesMaster, $routes);

        if (!empty($config['routes_prefix'])) {
            $this->filesystem->append($routesMaster, $config['routes_suffix']);
        }

        return true;
    }

    /**
     * Append to the factory.
     *
     * @param array $config
     *
     * @return bool
     */
    public function createFactory($config)
    {
        if (!is_dir(dirname($config['_path_factory_']))) {
            mkdir(dirname($config['_path_factory_']), 0777, true);
        }

        if (!file_exists($config['_path_factory_'])) {
            $this->filesystem->put($config['_path_factory_'], '<?php');
        }

        $factory = $this->filesystem->get($config['template_source'].'/Factory.txt');

        $factory = $this->tableService->getTableSchema($config, $factory);

        foreach ($config as $key => $value) {
            $factory = str_replace($key, $value, $factory);
        }

        return $this->filesystem->append($config['_path_factory_'], $factory);
    }

    /**
     * Create the facade.
     *
     * @param array $config
     *
     * @return bool
     */
    public function createFacade($config)
    {
        if (!is_dir($config['_path_facade_'])) {
            mkdir($config['_path_facade_'], 0777, true);
        }

        $facade = $this->filesystem->get($config['template_source'].'/Facade.txt');

        foreach ($config as $key => $value) {
            $facade = str_replace($key, $value, $facade);
        }

        $facade = $this->filesystem->put($config['_path_facade_'].'/'.$config['_camel_case_'].'.php', $facade);

        return $facade;
    }

    /**
     * Create the tests.
     *
     * @param array        $config
     * @param string|array $serviceOnly
     * @param string|array $apiOnly
     * @param string|array $withApi
     *
     * @return bool
     */
    public function createTests($config, $serviceOnly = '', $apiOnly = false, $withApi = false)
    {
        $testTemplates = $this->filesystem->allFiles($config['template_source'].'/Tests');

        $filteredTestTemplates = $this->testService->filterTestTemplates($testTemplates, $serviceOnly, $apiOnly, $withApi);

        foreach ($filteredTestTemplates as $testTemplate) {
            $test = $this->filesystem->get($testTemplate->getRealPath());
            $testName = $config['_camel_case_'].$testTemplate->getBasename('.'.$testTemplate->getExtension());
            $testDirectory = $config['_path_tests_'].'/'.strtolower($testTemplate->getRelativePath());

            if (!is_dir($testDirectory)) {
                mkdir($testDirectory, 0777, true);
            }

            $test = $this->tableService->getTableSchema($config, $test);

            foreach ($config as $key => $value) {
                $test = str_replace($key, $value, $test);
            }

            if (!$this->filesystem->put($testDirectory.'/'.$testName.'.php', $test)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Create the views.
     *
     * @param array $config
     *
     * @return bool
     */
    public function createViews($config)
    {
        if (!is_dir($config['_path_views_'].'/'.$config['_lower_casePlural_'])) {
            mkdir($config['_path_views_'].'/'.$config['_lower_casePlural_'], 0777, true);
        }

        $viewTemplates = 'Views';

        if ($config['bootstrap']) {
            $viewTemplates = 'BootstrapViews';
        }

        if ($config['semantic']) {
            $viewTemplates = 'SemanticViews';
        }

        $createdView = false;

        foreach (glob($config['template_source'].'/'.$viewTemplates.'/*') as $file) {
            $viewContents = $this->filesystem->get($file);
            $basename = str_replace('txt', 'php', basename($file));
            foreach ($config as $key => $value) {
                $viewContents = str_replace($key, $value, $viewContents);
            }
            $createdView = $this->filesystem->put($config['_path_views_'].'/'.$config['_lower_casePlural_'].'/'.$basename, $viewContents);
        }

        return $createdView;
    }

    /**
     * Create the Api.
     *
     * @param array $config
     *
     * @return bool
     */
    public function createApi($config)
    {
        $routesMaster = $config['_path_api_routes_'];

        if (!file_exists($routesMaster)) {
            $this->filesystem->put($routesMaster, "<?php\n\n");
        }

        if (!is_dir($config['_path_api_controller_'])) {
            mkdir($config['_path_api_controller_'], 0777, true);
        }

        $routes = $this->filesystem->get($config['template_source'].'/ApiRoutes.txt');

        foreach ($config as $key => $value) {
            $routes = str_replace($key, $value, $routes);
        }

        $this->filesystem->append($routesMaster, $routes);

        $request = $this->filesystem->get($config['template_source'].'/ApiController.txt');

        foreach ($config as $key => $value) {
            $request = str_replace($key, $value, $request);
        }

        $request = $this->filesystem->put($config['_path_api_controller_'].'/'.$config['_camel_case_'].'Controller.php', $request);

        return $request;
    }
}
