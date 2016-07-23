<?php

namespace Yab\CrudMaker\Generators;

use Exception;
use Illuminate\Filesystem\Filesystem;

/**
 * Generate the CRUD database components.
 */
class DatabaseGenerator
{
    protected $filesystem;

    public function __construct()
    {
        $this->filesystem = new Filesystem();
    }

    /**
     * Create the migrations.
     *
     * @param string                           $section
     * @param string                           $table
     * @param array                            $splitTable
     * @param \Yab\CrudMaker\Console\CrudMaker $command
     *
     * @return bool
     */
    public function createMigration($config, $section, $table, $splitTable, $command)
    {
        try {
            if (!empty($section)) {
                $migrationName = 'create_'.str_plural(strtolower(implode('_', $splitTable))).'_table';
                $tableName = str_plural(strtolower(implode('_', $splitTable)));
            } else {
                $migrationName = 'create_'.str_plural(strtolower($table)).'_table';
                $tableName = str_plural(strtolower($table));
            }

            $command->callSilent('make:migration', [
                'name'     => $migrationName,
                '--table'  => $tableName,
                '--create' => true,
                '--path'   => $this->getMigrationsPath($config, true),
            ]);

            return true;
        } catch (Exception $e) {
            throw new Exception('Could not create the migration', 1);
        }
    }

    /**
     * Create the Schema.
     *
     * @param string $section
     * @param string $table
     * @param array  $splitTable
     *
     * @return string
     */
    public function createSchema($config, $section, $table, $splitTable, $schema)
    {
        $migrationFiles = $this->filesystem->allFiles($this->getMigrationsPath($config));

        if (!empty($section)) {
            $migrationName = 'create_'.str_plural(strtolower(implode('_', $splitTable))).'_table';
        } else {
            $migrationName = 'create_'.str_plural(strtolower($table)).'_table';
        }

        $parsedTable = '';

        foreach (explode(',', $schema) as $key => $column) {
            $columnDefinition = explode(':', $column);
            if ($key === 0) {
                $parsedTable .= "\$table->$columnDefinition[1]('$columnDefinition[0]');\n";
            } else {
                $parsedTable .= "\t\t\t\$table->$columnDefinition[1]('$columnDefinition[0]');\n";
            }
        }

        foreach ($migrationFiles as $file) {
            if (stristr($file->getBasename(), $migrationName)) {
                $migrationData = file_get_contents($file->getPathname());
                $migrationData = str_replace("\$table->increments('id');", $parsedTable, $migrationData);
                file_put_contents($file->getPathname(), $migrationData);
            }
        }

        return $parsedTable;
    }

    /**
     * Get the migration path.
     *
     * @param array $config
     * @param bool  $relative
     *
     * @return string
     */
    private function getMigrationsPath($config, $relative = false)
    {
        if (!is_dir($config['_path_migrations_'])) {
            mkdir($config['_path_migrations_'], 0777, true);
        }

        if ($relative) {
            return str_replace(base_path(), '', $config['_path_migrations_']);
        }

        return $config['_path_migrations_'];
    }
}
