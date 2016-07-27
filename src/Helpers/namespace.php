<?php

if (!function_exists('app_namespace')) {
    function app_namespace()
    {
        return app('Yab\CrudMaker\Services\AppService')
            ->getAppNamespace();
    }
}
