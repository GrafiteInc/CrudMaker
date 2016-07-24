<?php

class CommandTest extends TestCase
{
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

    public function testCrudTableMaker()
    {
         $this->app['Illuminate\Contracts\Console\Kernel']->handle(
            $input = new \Symfony\Component\Console\Input\ArrayInput([
                'command' => 'crudmaker:table',
                '--no-interaction' => true
            ]),
            $output = new \Symfony\Component\Console\Output\BufferedOutput
        );

        $this->assertContains('Not enough arguments (missing: "table")', $output->fetch());
    }
}
