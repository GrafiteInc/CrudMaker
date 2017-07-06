<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/7/3
 * Time: 15:50
 */

namespace Louis\CrudMaker\Services;
use Illuminate\Filesystem\Filesystem;
//use Illuminate\Support\Facades\Schema;
use Illuminate\Routing\Router;
use Illuminate\Routing\Route;
use DB;


class CrudService
{
    protected $filesystem;
    protected $config;
    protected $bar;

    public function __construct(Filesystem $filesystem)
    {
        $this->filesystem = $filesystem;

    }

    /**
     * Create the model.
     *
     * @return void
     */
    public function createModel()
    {
        //create the model directory
        if (!$this->filesystem->exists($this->config['_path_model_'])) {
            $this->filesystem->makeDirectory($this->config['_path_model_'], 0777, true);
        }

        //get model template
        $model = $this->replaceDefaultTemplate("Model.txt");

        //replace fillable
        $tableColumns = $this->getModelColumns($this->config['_table_name_'], true);
        $model = str_replace('// create fillable', '"'.implode('","', array_keys($tableColumns)).'"', $model);

        //replace rules
        $rules = $this->getModelRules($this->config['_table_name_']);
        $model = str_replace('// create rules', $rules, $model);

        if (!empty($this->config['options-relationships'])) {
            $relationshipMethods = $this->getModelRelationships($this->config['options-relationships']);
            $model = str_replace('// create relationships', $relationshipMethods, $model);
        }

        $this->putIfNotExists($this->config['_path_model_'].$this->config['_ucCamel_case_'].'.php', $model);
        $this->bar->advance();
    }
    /**
     * Create the request.
     *
     * @return void
     */
    public function createRequest(){
        //create the reqest directory
        if (!$this->filesystem->exists($this->config['_path_request_'])) {
            $this->filesystem->makeDirectory($this->config['_path_request_'], 0777, true);
        }

        $baserequest = $this->replaceDefaultTemplate("BaseRequest.txt", $this->config);
        $this->putIfNotExists($this->config['_path_request_'].'/BaseRequest.php', $baserequest);

        $request = $this->replaceDefaultTemplate("Request.txt");
        $this->putIfNotExists($this->config['_path_request_'].$this->config['_ucCamel_case_'].'Request.php', $request);

        $this->bar->advance();
    }
    /**
     * Create the service.
     *
     * @return void
     */
    public function createService(){
        //create the reqest directory
        if (!$this->filesystem->exists($this->config['_path_service_'])) {
            $this->filesystem->makeDirectory($this->config['_path_request_'], 0777, true);
        }
        $service = $this->replaceDefaultTemplate("Service.txt");
        $this->putIfNotExists($this->config['_path_service_'].$this->config['_ucCamel_case_'].'Service.php', $service);

        $this->bar->advance();
    }

    /**
     * Create the controller.
     * @return void
     */
    public function createController()
    {
        if (!$this->filesystem->exists($this->config['_path_controller_'])) {
            $this->filesystem->makeDirectory($this->config['_path_controller_'], 0777, true);
        }

        $basecontroller = $this->replaceDefaultTemplate("BaseController.txt", $this->config);
        $this->putIfNotExists($this->config['_path_controller_'].'BaseController.php', $basecontroller);

        $controller = $this->replaceDefaultTemplate('/Controller.txt');
        $this->putIfNotExists( $this->config['_path_controller_'] . $this->config['_ucCamel_case_'] . 'Controller.php', $controller);

        $this->bar->advance();
    }
    /**
     * Create the api controller.
     *
     * @return void
     */
    public function createApiController(){
        if (!$this->filesystem->exists($this->config['_path_api_controller_'])) {
            $this->filesystem->makeDirectory($this->config['_path_api_controller_'], 0777, true);
        }

        $basecontroller = $this->replaceDefaultTemplate("BaseController.txt", $this->config);
        $this->putIfNotExists($this->config['_path_controller_'].'BaseController.php', $basecontroller);

        $controller = $this->replaceDefaultTemplate('/ApiController.txt');
        $this->putIfNotExists( $this->config['_path_api_controller_'].$this->config['_ucCamel_case_'].'Controller.php', $controller);

        $this->bar->advance();
    }
    /**
     * Create the route.
     *
     * @return void
     */
    public function createRoute(){
        $routes = "\r\n\r\nRoute::resource('" . $this->config['_lower_case_']. "', '" . $this->config['_ucCamel_case_'] . "Controller', ['except' => ['show']]);\r\n";
        $this->filesystem->append($this->config["_file_routes_"], $routes);

        $this->bar->advance();
    }

    /**
     * Create the api route.
     *
     * @return void
     */
    public function createApiRoute(){
        $routes = "\r\n\r\nRoute::resource('" . $this->config['_lower_case_']. "', '" . $this->config['_ucCamel_case_'] . "Controller', ['as' => '" . $this->config['_lower_case_']. "']);\r\n";
        $this->filesystem->append($this->config["_file_api_routes_"], $routes);

        $this->bar->advance();
    }
    /**
     * Create the view.
     * @return void
     */
    public function createViews(){
        if (!$this->filesystem->exists($this->config['_path_views_'].$this->config['_lower_case_'])) {
            $this->filesystem->makeDirectory($this->config['_path_views_'].$this->config['_lower_case_'], 0777, true);
        }

        foreach (glob($this->config['template_path'].'/Views/*') as $file) {
            $viewContents = $this->filesystem->get($file);
            $basename = str_replace('txt', 'php', basename($file));
            foreach ($this->config as $key => $value) {
                $viewContents = str_replace($key, $value, $viewContents);
            }
            $this->putIfNotExists($this->config['_path_views_'].$this->config['_lower_case_'].'/'.$basename, $viewContents);

        }

        $this->bar->advance();
    }


    private function replaceDefaultTemplate($template){
        $content = $this->filesystem->get($this->config["template_path"].$template);
        foreach ($this->config as $key => $value) {
            $content = str_replace($key, $value, $content);
        }
        return $content;
    }


    /**
     * Get Table Columns.
     *
     * @param string $table Table name
     *
     * @return array
     */
    private function getModelColumns($table, $allColumns = false){
        $prefix = DB::getTablePrefix();
        $tableColumns = DB::getDoctrineSchemaManager()->listTableColumns($prefix.$table);
        $tableTypeColumns = [];
        $badColumns = ['id', 'created_at', 'updated_at'];

        if ($allColumns) {
            $badColumns = [];
        }

        foreach ($tableColumns as $column) {
            if (!in_array($column->getName(), $badColumns)) {
                $tableTypeColumns[$column->getName()] = $column->toArray();
                $tableTypeColumns[$column->getName()]["type"] = $column->getType()->getName();
            }
        }
        return $tableTypeColumns;
    }

    private function getModelRules($table){
        $prefix = DB::getTablePrefix();
        $tableColumns = DB::getDoctrineSchemaManager()->listTableColumns($prefix.$table);
        $rules = "";
        foreach ($tableColumns as $column) {
            $rule = [];
            if ($column->getNotnull() && $column->getDefault() === null){
                $rule[] = "required";
            }
            switch ($column->getType()->getName()){
                case "integer":
                    $rule[] = "integer";
                    break;
                case "string":
                    $rule[] = "max:".$column->getLength();
                    break;
                case "datetime":
                    $rule[] = "date";
                    break;
            }
            if ($column->getAutoincrement()){
                $rule = [];
            }
            if (!empty($rule)){
                $rules .= "'".$column->getName() . "' => '" . implode("|", $rule) . "',\r\n        ";
            }
        }
        return $rules;
    }

    /**
     * Get Relationships function string
     * @param $relationships
     * @return string
     */
    private function getModelRelationships($relationships){
        $relationshipMethods = "";
        foreach (explode(',', $relationships) as $relationshipExpression) {
            $relation = explode('|', $relationshipExpression);
            $param = '';

            $param .= isset($relation[3]) ? ", '". $relation[3] ."'" : "";
            $param .= isset($relation[4]) ? ", '". $relation[4] ."'" : "";

            $method = str_singular($relation[2]);
            $relationshipMethods .= "\n\tpublic function ".$method.'() {';
            $relationshipMethods .= "\n\t\treturn \$this->$relation[0]($relation[1]::class$param);";
            $relationshipMethods .= "\n\t}\n\t";
        }
        return $relationshipMethods;
    }

    /**
     * Make a file if it doesnt exist.
     *
     * @param  string $file
     * @param  mixed $contents
     *
     * @return void
     */
    private function putIfNotExists($file, $contents)
    {

        if ($this->filesystem->exists($file)){
            $this->filesystem->delete($file);
        }
        if (!$this->filesystem->exists($file)) {
            return $this->filesystem->put($file, $contents);
        }

        return $this->filesystem->get($file);
    }

    /**
     * @param array $config
     * @param \Symfony\Component\Console\Helper\ProgressBar $bar
     */
    public function setConfig($config, $bar){
        $this->config = $config;
        $this->bar = $bar;
    }

}