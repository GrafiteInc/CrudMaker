<?php

function app_namespace()
{
    $appService = new \Yab\CrudMaker\Services\AppService();
    $namespace = $appService->getAppNamespace();

    return $namespace;
}