<?php

namespace Grafite\CrudMaker\Traits;

use Grafite\CrudMaker\Services\ValidatorService;

trait SchemaTrait
{
    /**
     * Callibrate the schema.
     *
     * @param string $schemaString
     *
     * @return array
     */
    public function calibrateDefinitions($schemaString)
    {
        // split schema string by comma only before the next attribute name
        // new attribute name is a comma followed by VALID_COLUMN_NAME_REGEX and then a colon
        return preg_split('/,(?='.ValidatorService::VALID_COLUMN_NAME_REGEX.':)/',$schemaString);
    }
}
