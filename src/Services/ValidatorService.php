<?php

namespace Grafite\CrudMaker\Services;

use Exception;
use Grafite\CrudMaker\Traits\SchemaTrait;

/**
 * CRUD Validator.
 */
class ValidatorService
{
    const VALID_COLUMN_NAME_REGEX = '[A-z\/_.]+';

    use SchemaTrait;

    /**
     * Validate the Schema.
     *
     * @param \Grafite\CrudMaker\Console\CrudMaker $command
     *
     * @return bool|Exception
     */
    public function validateSchema($command)
    {
        if ($command->option('schema')) {
            $definitions = $this->calibrateDefinitions($command->option('schema'));

            foreach ($definitions as $column) {
                $columnDefinition = explode(':', $column);
                if (!isset($columnDefinition[1])) {
                    throw new Exception('All schema columns require a column type.', 1);
                }

                $columnDetails = explode('|', $columnDefinition[1]);

                preg_match('('.self::VALID_COLUMN_NAME_REGEX.')', $columnDetails[0], $columnDetailsType);

                if (!in_array(camel_case($columnDetailsType[0]), $command->columnTypes)) {
                    throw new Exception($columnDetailsType[0].' is not in the array of valid column types: '.implode(', ', $command->columnTypes), 1);
                }
            }
        }

        return true;
    }

    /**
     * Validate the options.
     *
     * @param \Grafite\CrudMaker\Console\CrudMaker $command
     *
     * @return bool|Exception
     */
    public function validateOptions($command)
    {
        if ($command->option('ui') && !in_array($command->option('ui'), ['bootstrap', 'semantic'])) {
            throw new Exception('The UI you selected is not suppported. It must be: bootstrap or semantic.', 1);
        }

        if ((!is_null($command->option('schema')) && !$command->option('migration')) ||
            (!is_null($command->option('relationships')) && !$command->option('migration'))
        ) {
            throw new Exception('In order to use Schema or Relationships you need to use Migrations', 1);
        }

        return true;
    }
}
