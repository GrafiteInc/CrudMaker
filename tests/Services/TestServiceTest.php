<?php

use Illuminate\Filesystem\Filesystem;
use Yab\CrudMaker\Services\TestService;

class TestServiceTest extends TestCase
{
    protected $service;
    protected $filesystem;

    public function setUp()
    {
        parent::setUp();
        $this->service = app(TestService::class);
        $this->filesystem = new Filesystem();
    }

    public function testIsServiceTest()
    {
        $result = $this->service->isServiceTest('TestTableRepository.php');
        $this->assertTrue($result);
    }

    public function testIsServiceTestFalse()
    {
        $result = $this->service->isServiceTest('TestTableConrtoller.php');
        $this->assertTrue(!$result);
    }

    public function testFilterTestTemplates()
    {
        $testTemplates = $this->filesystem->allFiles(__DIR__.'/../../src/Templates/Laravel/Tests');
        $result = $this->service->filterTestTemplates($testTemplates, '', false, false);

        $this->assertTrue(is_array($result));
    }
}
