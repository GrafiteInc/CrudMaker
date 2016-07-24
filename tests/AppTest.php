<?php

class AppTest extends Orchestra\Testbench\TestCase
{
    protected $app;

    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('database.default', 'testbench');
        $app['config']->set('database.connections.testbench', [
            'driver'   => 'sqlite',
            'database' => ':memory:',
            'prefix'   => '',
        ]);
        $app->make('Illuminate\Contracts\Http\Kernel');
    }

    protected function getPackageProviders($app)
    {
        return [
            \Yab\CrudMaker\CrudMakerProvider::class,
        ];
    }

    protected function getPackageAliases($app)
    {
        return [];
    }

    public function setUp()
    {
        parent::setUp();
        $this->withFactories(__DIR__.'/../src/Models/Factories');
        $this->artisan('migrate', [
            '--database' => 'testbench',
            '--realpath' => realpath(__DIR__.'/../src/Migrations'),
        ]);
        $this->artisan('vendor:publish', [
            '--provider' => 'Yab\CrudMaker\CrudMakerProvider',
            '--force'    => true,
        ]);
        $this->withoutMiddleware();
        $this->withoutEvents();
    }

    public function testCrudMaker()
    {
         $this->app['Illuminate\Contracts\Console\Kernel']->handle(
            $input = new \Symfony\Component\Console\Input\ArrayInput([
                'command' => 'crudmaker:new',
                '--no-interaction' => true
            ]),
            $output = new \Symfony\Component\Console\Output\BufferedOutput
        );

        $this->assertContains('Not enough arguments (missing: "table")', $output->fetch());
    }
}
