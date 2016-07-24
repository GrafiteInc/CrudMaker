<?php

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
        $this->command = Mockery::mock(\Illuminate\Console\Command::class);
        $this->command->shouldReceive('callSilent')->andReturnUsing(function ($command, $data) {
            \Artisan::call($command, $data);
        });
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

        $this->generator->createMigration('random_string', 'TestTable', 'another_random_string', $this->command);
    }

    public function testCreateMigrationSuccess()
    {
        $this->createMigration();
    }

    public function testCreateMigrationSuccessAlternativeLocation()
    {
        $config = [
            '_path_migrations_' => base_path('alternative_migrations_location')
        ];

        $this->createMigration('alternative_migrations_location');
        $this->assertCount(1, glob(base_path('alternative_migrations_location').'/*'));
    }

    public function testCreateSchema()
    {
        $migrations = $this->createMigration();
        $schemaForm = $this->generator->createSchema(
            $this->config,
            '',
            'TestTable',
            [],
            'id:increments,name:string',
            $this->artisanMock
        );

        $this->assertContains('testtables', file_get_contents($migrations[0]));
        $this->assertContains('table->increments(\'id\')', file_get_contents($migrations[0]));

        $this->assertContains('table->increments', $schemaForm);
        $this->assertContains('table->string(\'name\')', $schemaForm);
    }

    public function testCreateSchemaAlternativeLocation()
    {
        $migrations = $this->createMigration('alternative_migrations_location');

        $schemaForm = $this->generator->createSchema(
            $this->config,
            '',
            'TestTable',
            [],
            'id:increments,name:string',
            $this->artisanMock
        );

        $this->assertContains('table->increments', $schemaForm);
        $this->assertContains('table->string(\'name\')', $schemaForm);
    }

    private function createMigration($location = null)
    {
        if ($location) {
            $this->config = [
                '_path_migrations_' => base_path($location)
            ];
        }

        $migrationWasMade = $this->generator->createMigration($this->config, '', 'TestTable', [], $this->command);
        $migrations = glob($this->config['_path_migrations_'].'/*');

        $this->assertTrue($migrationWasMade);
        $this->assertCount(1, $migrations);

        return $migrations;
    }

    public function tearDown()
    {
        parent::tearDown();
        array_map('unlink', glob($this->config['_path_migrations_'] . '/*'));
    }
}
