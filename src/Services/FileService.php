<?php

namespace Grafite\CrudMaker\Services;

class FileService
{
    public function mkdir($path, $mode, $recursive)
    {
        if (! is_dir($path)) {
            mkdir($path, $mode, $recursive);
        }
    }
}
