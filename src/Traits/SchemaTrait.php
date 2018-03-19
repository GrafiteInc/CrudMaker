<?php

namespace Grafite\CrudMaker\Traits;

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
        $defs = explode(',', $schemaString);

        foreach ($defs as $key => $def) {
            if (!strpos($def, ':')) {
                $defs[$key - 1] = $defs[$key - 1].','.$def;
                unset($defs[$key]);
            }
        }

        return $defs;
    }
}
