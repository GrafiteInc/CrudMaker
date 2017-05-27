<?php

namespace Yab\CrudMaker\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Yab\CrudMaker\Traits\SchemaTrait;

class TableService
{
    use SchemaTrait;

    /**
     * Prepare a string of the table.
     *
     * @param string $table
     *
     * @return string
     */
    public function prepareTableDefinition($table)
    {
        $tableDefintion = '';
        $definitions = $this->calibrateDefinitions($table);

        foreach ($definitions as $column) {
            $columnDefinition = explode(':', $column);
            $tableDefintion .= "\t\t'$columnDefinition[0]',\n";
        }

        return $tableDefintion;
    }

    /**
     * Prepare a table array example.
     *
     * @param string $table
     *
     * @return string
     */
    public function prepareTableExample($table)
    {
        $tableExample = '';
        $definitions = $this->calibrateDefinitions($table);

        foreach ($definitions as $key => $column) {
            $columnDefinition = explode(':', $column);
            $example = $this->createExampleByType($columnDefinition[1]);

            if ($key === 0) {
                $tableExample .= "'$columnDefinition[0]' => '$example',\n";
            } else {
                $tableExample .= "\t\t'$columnDefinition[0]' => '$example',\n";
            }
        }

        return $tableExample;
    }

    /**
     * Build a table schema.
     *
     * @param array  $config
     * @param string $string
     *
     * @return string
     */
    public function getTableSchema($config, $string)
    {
        if (!empty($config['schema'])) {
            $string = str_replace('// _camel_case_ table data', $this->prepareTableExample($config['schema']), $string);
        }

        return $string;
    }

    /**
     * Create an example by type for table definitions.
     *
     * @param string $type
     *
     * @return mixed
     */
    public function createExampleByType($type)
    {
        $typeArray = [
            'bigIncrements' => 1,
            'increments' => 1,
            'string' => 'laravel',
            'boolean' => 1,
            'binary' => 'Its a bird, its a plane, no its Superman!',
            'char' => 'a',
            'ipAddress' => '192.168.1.1',
            'macAddress' => 'X1:X2:X3:X4:X5:X6',
            'json' => json_encode(['json' => 'test']),
            'text' => 'I am Batman',
            'longText' => 'I am Batman',
            'mediumText' => 'I am Batman',
            'dateTime' => date('Y-m-d h:i:s'),
            'date' => date('Y-m-d'),
            'time' => date('h:i:s'),
            'timestamp' => time(),
            'float' => 1.1,
            'decimal' => 1.1,
            'double' => 1.1,
            'integer' => 1,
            'bigInteger' => 1,
            'mediumInteger' => 1,
            'smallInteger' => 1,
            'tinyInteger' => 1,
        ];

        if (isset($typeArray[$type])) {
            return $typeArray[$type];
        }

        return 1;
    }

    /**
     * Table definitions.
     *
     * @param string $table
     *
     * @return string
     */
    public function tableDefintion($table)
    {
        $columnStringArray = [];
        $columns = $this->getTableColumns($table, true);

        foreach ($columns as $key => $column) {
            if ($key === 'id') {
                $column['type'] = 'increments';
            }

            $columnStringArray[] = $key.':'.$this->columnNameCheck($column['type']);
        }

        $columnString = implode(',', $columnStringArray);

        return $columnString;
    }

    /**
     * Corrects a column type for Schema building.
     *
     * @param string $column
     *
     * @return string
     */
    private function columnNameCheck($column)
    {
        $columnsToAdjust = [
            'datetime' => 'dateTime',
            'smallint' => 'smallInteger',
            'bigint' => 'bigInteger',
            'datetimetz' => 'timestamp',
        ];

        if (isset($columnsToAdjust[$column])) {
            return $columnsToAdjust[$column];
        }

        return $column;
    }

    /**
     * Get Table Columns.
     *
     * @param string $table Table name
     *
     * @return array
     */
    public function getTableColumns($table, $allColumns = false)
    {
        $tableColumns = Schema::getColumnListing($table);

        $tableTypeColumns = [];
        $badColumns = ['id', 'created_at', 'updated_at'];

        if ($allColumns) {
            $badColumns = [];
        }

        foreach ($tableColumns as $column) {
            if (!in_array($column, $badColumns)) {
                //$type = DB::connection()->getDoctrineColumn($table, $column)->getType()->getName();
                $type = Schema::getColumnType($table, $column);
                $tableTypeColumns[$column]['type'] = $type;
            }
        }

        return $tableTypeColumns;
    }
}
