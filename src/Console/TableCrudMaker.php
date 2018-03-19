<?php

namespace Grafite\CrudMaker\Console;

use Exception;
use Illuminate\Console\DetectsApplicationNamespace;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Grafite\CrudMaker\Services\TableService;

class TableCrudMaker extends Command
{
    use DetectsApplicationNamespace;

    /**
     * The console command name.
     *
     * @var string
     */
    protected $signature = 'crudmaker:table {table}
        {--api : Creates an API Controller and Routes}
        {--ui= : Select one of bootstrap|semantic for the UI}
        {--serviceOnly : Does not generate a Controller or Routes}
        {--withFacade : Creates a facade that can be bound in your app to access the CRUD service}
        {--relationships= : Define the relationship ie: hasOne|App\Comment|comment,hasOne|App\Rating|rating or relation|class|column (without the _id)}
    ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generates a magical CRUD from an existing table';

    /**
     * Generate a CRUD stack.
     *
     * @return mixed
     */
    public function handle()
    {
        $filesystem = new Filesystem();
        $tableService = new TableService();
        $table = (string) $this->argument('table');
        $tableDefintion = $tableService->tableDefintion($table);

        if (empty($tableDefintion)) {
            throw new Exception("There is no table definition for $table. Are you sure you spelled it correctly? Table names are case sensitive.", 1);
        }

        $this->call('crudmaker:new', [
            'table' => $table,
            '--api' => $this->option('api'),
            '--ui' => $this->option('ui'),
            '--serviceOnly' => $this->option('serviceOnly'),
            '--withFacade' => $this->option('withFacade'),
            '--migration' => true,
            '--relationships' => $this->option('relationships'),
            '--schema' => $tableDefintion,
        ]);

        // Format the table name accordingly
        // usecase: OrderProducts turns into order_products
        $table_name = str_plural(strtolower(snake_case($table)));

        $migrationName = 'create_'.$table_name.'_table';
        $migrationFiles = $filesystem->allFiles(base_path('database/migrations'));

        foreach ($migrationFiles as $file) {
            if (stristr($file->getBasename(), $migrationName)) {
                $migrationData = file_get_contents($file->getPathname());
                if (stristr($migrationData, 'updated_at')) {
                    $migrationData = str_replace('$table->timestamps();', '', $migrationData);
                }
                file_put_contents($file->getPathname(), $migrationData);
            }
        }

        $this->line("\nYou've generated a CRUD for the table: ".$table);
        $this->line("\n\nYou may wish to add this as your testing database");
        $this->line("'testing' => [ 'driver' => 'sqlite', 'database' => ':memory:', 'prefix' => '' ],");
        $this->info("\n\nCRUD for $table is done.");
    }
}
