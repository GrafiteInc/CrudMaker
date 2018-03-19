<?php

if (!function_exists('app_namespace')) {
    function app_namespace()
    {
        return app('Grafite\CrudMaker\Services\AppService')
            ->getAppNamespace();
    }
}
