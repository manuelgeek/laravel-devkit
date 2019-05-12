<?php

namespace LaravelDevkit\Console;


use LaravelDevkit\Devkit\Repos\ModuleRepo;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use LaravelDevkit\Factory;

/**
 * @method notify(string $string, string $string1)
 */
class ModuleCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'kit:module {modulecommand} {--module=} {--name=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command is used to manage modules';
    protected $files;
    public $moduleManifest;
    public $factory;
    public $moduleName;
    public $container;

    /**
     * ModuleCommand constructor.
     * @param Filesystem $files
     * @param Factory $factory
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
//        $this->notify('Hello Web Artisan', 'Love beautiful code? We do too!');
        $getArgument = $this->argument('modulecommand');
        $response = $this->implementCommand($getArgument);

//        $this->info($this->factory->displayThankYou());
    }

    /**
     * @param $argument
     */
    public function implementCommand($argument)
    {
        switch ($argument){
            case "list":
                $isFileAvailale = $this->files->exists('config/laravel-devkit-modules.php');
                if(! $isFileAvailale){
                    $this->error("Laravel Devkit config file is missing. Run php artisan kit:install command to publish it");
                }
                else{

                    $errorMsg = "No module installed";

                    $getModules = ModuleRepo::getModules();

                    if (count($getModules) > 0){
                        $modules = "";
                        $i = 1;

                        foreach ($getModules as $module => $getModule){
                            $modules =$modules. $i. "." . $module." ";
                            $i = $i + 1;
                        }

                        $this->line("\n<comment>Modules</comment> : ". $modules);
                        $userModules = $this->ask("Enter the name of the module(s) you want to install separated with comma OR <comment>q</comment> to exit");

//                if ($this->confirm("Confirm installation")){
                        if ($userModules){
                            if ($userModules != 'q') {
                                $msg = null;
                                if (str_contains($userModules, ",")) {
                                    foreach (explode( ",",$userModules) as $userModule) {
                                        if ($moduleSource = ModuleRepo::module()->getModuleSource($userModule)) {
                                            $this->moduleManifest = ModuleRepo::getModuleManifest($moduleSource);

                                            $this->response($this->factory->iWantTo()->installModule($this->moduleManifest));

                                        } else {
                                            $this->line("Model does not exist");
                                        }
                                    }
                                } else {
                                    if ($moduleSource = ModuleRepo::module()->getModuleSource($userModules)) {

                                        $this->moduleManifest = ModuleRepo::getModuleManifest($moduleSource);

                                        $this->response($this->factory->iWantTo()->installModule($this->moduleManifest));

                                    } else {
                                        $this->error("Model does not exist");
                                    }
                                }
                            }
                            else{
                                $this->line('Happy coding...');
                            }
                        }
                        else{
                            $errorMsg = "You did not enter any model";

                            $this->line($errorMsg);
                        }
//                }
                    }
                    else{
                        $this->line($errorMsg);
                    }
                }
                break;
            case "create":
                $this->moduleName = $this->ask("Module Name : <comment>vendor/module-name</comment>");

                if (str_contains($this->moduleName, "/")){

                    $modName = explode("/",$this->moduleName);

                    $this->container['name'] = ucwords($modName[0])."/".ucwords($modName[1]);
                    $this->container['version']     = '1.0';
                    $this->container['theme']     = 'default';
                    $this->container['vendor']     = $modName[0];
                    $this->container['email']     = $this->ask("Email") ?: 'example@email.com';
                    $this->container['license']     = $this->ask("License <comment>MIT</comment>") ?: 'MIT';
                    $this->container['description'] = $this->ask("Module description (Optional but recommended)") ?: 'This is the description for the ' . $this->container['name'] . ' module.';
                    $this->container['base'] = studly_case(str_replace("/","_",$this->container['name']));
                    $this->container['module_slug'] = str_slug($modName[1]);
                    $this->container['provider'] = config('laravel-devkit.provider');
                    $this->container['namespace'] = config('laravel-devkit.namespace.module').$this->container['base'];
                    $this->container['source']    = config('laravel-devkit.locations.modules')."/".$this->container['base'];

                    $this->response($this->factory->iWantTo()->createModule($this->container));

                }
                else{
                    $this->error("Wrong naming format");
                }

                break;
            case "controller":
                $this->moduleName = $this->option('module');
                $controllerName = $this->option('name');

                if($this->moduleName && $controllerName){

                }
                else{
                    $this->error("Missing arguments");
                }
        }
    }

    public function response($response)
    {
        if ($response['type'] == "info"){
            return $this->info($response['message']);
        }
        if ($response['type'] == "error"){
            return $this->error($response['message']);
        }
    }
}
