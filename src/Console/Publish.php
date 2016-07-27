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

    /**
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * Publish constructor.
     */
    public function __construct()
    {
        $this->filesystem = new Filesystem();
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
     * Copy a directory and its content
     *
     * @param $directory
     * @param $destination
     * @return bool|int
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function copyDirectory($directory, $destination)
    {
        $files = $this->filesystem->allFiles($directory);
        $fileDeployed = false;
        $this->filesystem->copyDirectory($directory, $destination);

        foreach ($files as $file) {
            $fileContents = $this->filesystem->get($file);
            $fileDeployed = $this->filesystem->put($destination.'/'.$file->getRelativePathname(), $fileContents);
        }

        return $fileDeployed;
    }

    /**
     *  Publish config files for Lumen
     */
    private function publishConfig()
    {
        if (!is_dir(getcwd().'/config')) {
            $this->copyDirectory(__DIR__.'/../config', getcwd());
        }

        $this->info("\n\nLumen config file has been created");
    }

    /**
     *  Publish templates files for Lumen
     */
    private function publishTemplates()
    {
        if (!is_dir(getcwd().'/ressources/crudmaker/crud')) {
            $this->copyDirectory(__DIR__.'/../Templates/Lumen', getcwd().'/ressources/crudmaker');
            $this->filesystem->moveDirectory(getcwd().'/ressources/crudmaker/Lumen', getcwd().'/ressources/crudmaker/crud');
        }

        $this->info("\n\nLumen templates files has been created");
    }
}