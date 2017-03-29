<?php

namespace Yab\CrudMaker\Generators;

use Exception;
use Illuminate\Filesystem\Filesystem;
use Yab\CrudMaker\Services\FileService;
use Yab\CrudMaker\Traits\SchemaTrait;

/**
 * Generate the CRUD database components.
 */
class DatabaseGenerator
{
    use SchemaTrait;

    protected $filesystem;
    protected $fileService;

    public function __construct()
    {
        $this->filesystem = new Filesystem();
        $this->fileService = new FileService();
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
                $migrationName = 'create_'.str_plural(strtolower(snake_case($table))).'_table';
                $tableName = str_plural(strtolower(snake_case($table)));
            }

            $command->callSilent('make:migration', [
                'name' => $migrationName,
                '--table' => $tableName,
                '--create' => true,
                '--path' => $this->getMigrationsPath($config, true),
            ]);

            return true;
        } catch (Exception $e) {
            throw new Exception('Could not create the migration: '.$e->getMessage(), 1);
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
            $migrationName = 'create_'.str_plural(strtolower(snake_case($table))).'_table';
        }

        $parsedTable = '';

        $definitions = $this->calibrateDefinitions($schema);

        foreach ($definitions as $key => $column) {
            $columnDefinition = explode(':', $column);
            $columnDetails = explode('|', $columnDefinition[1]);
            $columnDetailString = $this->createColumnDetailString($columnDetails);

            if ($key === 0) {
                $parsedTable .= $this->getSchemaString($columnDetails, $columnDefinition, $columnDetailString);
            } else {
                $parsedTable .= "\t\t".$this->getSchemaString($columnDetails, $columnDefinition, $columnDetailString);
            }
        }

        if (isset($config['relationships']) && !is_null($config['relationships'])) {
            $relationships = explode(',', $config['relationships']);
            foreach ($relationships as $relationship) {
                $relation = explode('|', $relationship);

                if (isset($relation[2])) {
                    if (!stristr($parsedTable, "integer('$relation[2]')")) {
                        $parsedTable .= "\t\t\t\$table->integer('$relation[2]');\n";
                    }
                }
            }
        }

        foreach ($migrationFiles as $file) {
            if (stristr($file->getBasename(), $migrationName)) {
                $migrationData = $this->filesystem->get($file->getPathname());
                $migrationData = str_replace("\$table->increments('id');", $parsedTable, $migrationData);
                $this->filesystem->put($file->getPathname(), $migrationData);
            }
        }

        return $parsedTable;
    }

    public function getSchemaString($columnDetails, $columnDefinition, $columnDetailString)
    {
        if (strpos($columnDetails[0], '(')) {
            $injectedColumn = explode('(', $columnDetails[0]);
            $columnDef = $injectedColumn[0].'(\''.$columnDefinition[0].'\','.$injectedColumn[1];
        } else {
            $columnDef = $columnDetails[0].'(\''.$columnDefinition[0].'\')';
        }

        return "\$table->$columnDef$columnDetailString;\n";
    }

    /**
     * Create a column detail string.
     *
     * @param array $columnDetails
     *
     * @return string
     */
    public function createColumnDetailString($columnDetails)
    {
        $columnDetailString = '';

        if (count($columnDetails) > 1) {
            array_shift($columnDetails);

            foreach ($columnDetails as $key => $detail) {
                if ($key === 0) {
                    $columnDetailString .= '->';
                }
                $columnDetailString .= $this->columnDetail($detail);
                if ($key != count($columnDetails) - 1) {
                    $columnDetailString .= '->';
                }
            }

            return $columnDetailString;
        }
    }

    /**
     * Determine column detail string.
     *
     * @param array $detail
     *
     * @return string
     */
    public function columnDetail($detail)
    {
        $columnDetailString = '';

        if (stristr($detail, '(')) {
            $columnDetailString .= $detail;
        } else {
            $columnDetailString .= $detail.'()';
        }

        return $columnDetailString;
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
        $this->fileService->mkdir($config['_path_migrations_'], 0777, true);

        if ($relative) {
            return str_replace(base_path(), '', $config['_path_migrations_']);
        }

        return $config['_path_migrations_'];
    }
}
