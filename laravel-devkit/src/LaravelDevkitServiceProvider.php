<?php
/**
 * Created by PhpStorm.
 * User: marvincollins
 * Date: 1/1/19
 * Time: 1:58 PM
 */
namespace LaravelDevkit;

use Illuminate\Database\ConnectionResolverInterface;
use Illuminate\Database\Migrations\MigrationRepositoryInterface;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Cache;
use LaravelDevkit\Console\ModuleControllerCommand;
use LaravelDevkit\Console\ModuleFactoryCommand;
use LaravelDevkit\Console\ModuleMigrateCommand;
use LaravelDevkit\Console\ModuleMigrateFreshCommand;
use LaravelDevkit\Console\ModuleMigrateRollbackCommand;
use LaravelDevkit\Console\ModuleMigrateSeedCommand;
use LaravelDevkit\Console\ModuleMigrationCommand;
use LaravelDevkit\Console\ModuleModelCommand;
use LaravelDevkit\Console\ModuleRequestCommand;
use LaravelDevkit\Console\ModuleSeederCommand;
use LaravelDevkit\Devkit\Repos\ModuleRepo;
use LaravelDevkit\Console\InstallCommand;
use LaravelDevkit\Console\ModuleCommand;
use Illuminate\Support\ServiceProvider;

class LaravelDevkitServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/config/laravel-devkit.php' => config_path('laravel-devkit.php'),
            __DIR__ . '/config/laravel-devkit-modules.php' => config_path('laravel-devkit-modules.php'),
            __DIR__ . '/../alab/views/layouts' => resource_path('views/alab/layouts'),
            __DIR__ . '/../alab/views/auth' => resource_path('views/alab/auth'),
        ], 'kit-install');

        $this->publishes([
            __DIR__ . '/config/laravel-devkit.php' => config_path('laravel-devkit.php'),
            __DIR__ . '/config/laravel-devkit-modules.php' => config_path('laravel-devkit-modules.php'),
        ], 'config');

        $this->publishes([
            __DIR__ . '/../alab/public' => public_path('alab')
        ], 'public');

        if ($this->app->runningInConsole()){
            $this->app->bindIf(ConnectionResolverInterface::class, 'db');
            $this->app->bindIf(MigrationRepositoryInterface::class, 'migration.repository');
            $this->commands([
                InstallCommand::class,
                ModuleCommand::class,
                ModuleControllerCommand::class,
                ModuleMigrationCommand::class,
                ModuleModelCommand::class,
                ModuleSeederCommand::class,
                ModuleRequestCommand::class,
                ModuleFactoryCommand::class,
                ModuleMigrateCommand::class,
                ModuleMigrateFreshCommand::class,
                ModuleMigrateRollbackCommand::class,
                ModuleMigrateSeedCommand::class
            ]);
        }

        $this->registerServiceProviders();

    }

    public function register()
    {
        $this->registerHelper();
        $this->registerConfig();
    }

    public function registerConfig()
    {
        $this->mergeConfigFrom(__DIR__ . '/config/laravel-devkit.php','laravel-devkit');
        $this->mergeConfigFrom(__DIR__ . '/config/laravel-devkit-modules.php','laravel-devkit-modules');
    }

    public function registerServiceProviders()
    {
        $modulesNamespace = config('laravel-devkit.namespace.module');

        $modules = array_flatten(Cache::get('modules')); //config('laravel-devkit-modules.installed');

        if ($modules){
            foreach ($modules as $key => $module){
                if ((new Filesystem())->isDirectory(config('laravel-devkit.locations.modules').'/'.$module) && class_exists($modulesNamespace.$module.'\\Providers\\ModuleServiceProvider')){
                    $this->app->register(module_class('Providers\\ModuleServiceProvider', $module));
                }
                else{
                    ModuleRepo::cleanModules($module);
                }
            }
        }

    }

    public function registerHelper()
    {
        $file = __DIR__.'/Support/helpers.php';

        if (file_exists($file)){
            require_once($file);
        }
    }
}