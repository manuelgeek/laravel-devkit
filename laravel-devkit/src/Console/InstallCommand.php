<?php

namespace LaravelDevkit\Console;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use LaravelDevkit\Factory;

class InstallCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'kit:install {--force: Overwrite previous installation}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command install the initial resources and module for Apps:Lab Laravel Starter Kit';
    protected $files;
    protected $factory;

    /**
     * Create a new command instance.
     *
     * @param Filesystem $files
     */
    public function __construct(Filesystem $files, Factory $factory)
    {
        parent::__construct();
        $this->files = $files;
        $this->factory = $factory;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
//        vendor publish
        $this->info("Installing ....");
        $this->call('vendor:publish',[
            '--provider' => "LaravelDevkit\LaravelDevkitServiceProvider"
        ]);

        $this->line("<comment>Installation completed successfully</comment>");

        $this->notify("Devkit","Devkit installation completed ... Happy coding");

        $this->info($this->factory->displayThankYou());
    }

    public function makeDirectories()
    {
        $directories = config('locations.locations');

        foreach ($directories as $directory){
            if(!$this->files->exists($directory)){
                $this->files->makeDirectory($directory,0755, true);
            }
        }

    }
}
