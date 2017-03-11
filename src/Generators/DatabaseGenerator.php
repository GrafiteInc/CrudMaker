<?php

namespace Yab\CrudMaker\Generators;

use Exception;
use Illuminate\Filesystem\Filesystem;
use Yab\CrudMaker\Services\FileService;

/**
 * Generate the CRUD database components.
 */
class DatabaseGenerator
{
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
                $migrationName = 'create_'.str_plural(strtolower($table)).'_table';
                $tableName = str_plural(strtolower($table));
            }

            $command->callSilent('make:migration', [
                'name' => $migrationName,
                '--table' => $tableName,
                '--create' => true,
                '--path' => $this->getMigrationsPath($config, true),
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
            $columnDetails = explode('|', $columnDefinition[1]);
            $columnDetailString = $this->createColumnDetailString($columnDetails);

            if ($key === 0) {
                $parsedTable .= "\$table->$columnDetails[0]('$columnDefinition[0]')$columnDetailString;\n";
            } else {
                $parsedTable .= "\t\t\t\$table->$columnDetails[0]('$columnDefinition[0]')$columnDetailString;\n";
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
