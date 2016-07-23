<?php

use org\bovigo\vfs\vfsStream;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Container\Container as Container;
use Illuminate\Support\Facades\Facade as Facade;
use Yab\CrudMaker\Generators\DatabaseGenerator;

class DatabaseGeneratorTest extends AppTest
{
    protected $generator;
    protected $config;
    protected $artisanMock;

    public function setUp()
    {
        parent::setUp();
        $this->generator = new DatabaseGenerator();
        $this->config = [
            '_path_migrations_' => base_path('database/migrations')
        ];

        $config = $this->config;
        $this->artisanMock = Mockery::mock('Illuminate\Console\Command');
        $this->artisanMock->shouldReceive('callSilent')
            ->andReturn($this->artisanMock)
            ->shouldReceive('make:migration')
            ->with([
                'name'      => 'create_testtables_table',
                '--table'   => 'testtables',
                '--create'  => 'true',
                '--path'    => '/database/migrations',
            ])
            ->andReturn(true);
    }

    public function testCreateMigrationFail()
    {
        $this->setExpectedException('Exception');
        $this->generator->createMigration('alskfdjbajlksbdfl', 'TestTable', 'lkdblkabflabsd');
    }

    public function testCreateMigrationSuccess()
    {
        $test = $this->generator->createMigration(
            $this->config,
            '',
            'TestTable',
            [],
            $this->artisanMock
        );

        $this->assertTrue($test);
    }

    public function testCreateMigrationSuccessAlternativeLocation()
    {
        $config = [
            '_path_migrations_' => base_path('alternative_migrations_location')
        ];

        $test = $this->generator->createMigration($config, '', 'TestTable', [], $this->artisanMock);

        $this->assertTrue($test);
    }

    public function testCreateSchema()
    {
        $test = $this->generator->createMigration($this->config, '', 'TestTable', [], $this->artisanMock);
        $this->assertTrue($test);

        $otherTest = $this->generator->createSchema($this->config, '', 'TestTable', [], 'id:increments,name:string', $this->artisanMock);

        $this->assertTrue((bool) stristr($otherTest, "table->increments('id')"));
        $this->assertTrue((bool) stristr($otherTest, "table->string('name')"));
    }

    public function testCreateSchemaAlternativeLocation()
    {
        $config = [
            '_path_migrations_' => base_path('alternative_migrations_location')
        ];

        $test = $this->generator->createMigration($config, '', 'TestTable', [], $this->artisanMock);
        $this->assertTrue($test);

        $otherTest = $this->generator->createSchema($config, '', 'TestTable', [], 'id:increments,name:string', $this->artisanMock);

        $this->assertTrue((bool) stristr($otherTest, "table->increments('id')"));
        $this->assertTrue((bool) stristr($otherTest, "table->string('name')"));
    }
}
