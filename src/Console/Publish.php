<?php

namespace Yab\CrudMaker\Console;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

class Publish extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $signature = 'crudmaker:init';
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Crud initilization for Lumen based installation';

    protected $fileSystem;

    public function __construct()
    {
        parent::__construct();
        $this->fileSystem = new Filesystem();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->publishConfig();
        $this->publishTemplates();
    }

    /**
     * Copy a directory and its content.
     *
     * @param $directory
     * @param $destination
     *
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     *
     * @return bool|int
     */
    public function copyDirectory($directory, $destination)
    {
        $files = $this->fileSystem->allFiles($directory);
        $fileDeployed = false;
        $this->fileSystem->copyDirectory($directory, $destination);

        foreach ($files as $file) {
            $fileContents = $this->fileSystem->get($file);
            $fileDeployed = $this->fileSystem->put($destination.'/'.$file->getRelativePathname(), $fileContents);
        }

        return $fileDeployed;
    }

    /**
     *  Publish config files for Lumen.
     */
    private function publishConfig()
    {
        if (!file_exists(getcwd().'/config/crudmaker.php')) {
            $this->copyDirectory(__DIR__.'/../../config', getcwd().'/config');
            $this->info("\n\nLumen config file have been published");
        } else {
            $this->error('Lumen config files has already been published');
        }
    }

    /**
     *  Publish templates files for Lumen.
     */
    private function publishTemplates()
    {
        if (!$this->fileSystem->isDirectory(getcwd().'/resources/crudmaker/crud')) {
            $this->copyDirectory(__DIR__.'/../Templates/Lumen', getcwd().'/resources/crudmaker/crud');
            $this->info("\n\nLumen templates files have been published");
        } else {
            $this->error('Lumen templates files has already been published');
        }
    }
}
