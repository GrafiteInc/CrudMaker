<?php

namespace Yab\CrudMaker\Services;

class ModelService
{
    /**
     * Prepare a models relationships.
     *
     * @param array $relationships
     *
     * @return string
     */
    public function prepareModelRelationships($relationships)
    {
        $relationshipMethods = '';

        foreach ($relationships as $relation) {
            if (!isset($relation[2])) {
                $relationEnd = explode('\\', $relation[1]);
                $relation[2] = strtolower(end($relationEnd));
            }

            $method = str_singular($relation[2]);

            if (stristr($relation[0], 'many')) {
                $method = str_plural($relation[2]);
            }

            $relationshipMethods .= "\n\tpublic function ".$method.'() {';
            $relationshipMethods .= "\n\t\treturn \$this->$relation[0]($relation[1]::class);";
            $relationshipMethods .= "\n\t}";
        }

        return $relationshipMethods;
    }
}
