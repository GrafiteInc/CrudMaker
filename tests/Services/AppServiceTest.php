<?php

use Yab\CrudMaker\Services\AppService;

class AppServiceTest extends TestCase
{
    protected $service;

    public function setUp()
    {
        parent::setUp();
        $this->service = app(AppService::class);
    }

    public function testGetAppNamespace()
    {
        $result = $this->service->getAppNamespace();
        $this->assertEquals($result, 'App\\');
    }
}
