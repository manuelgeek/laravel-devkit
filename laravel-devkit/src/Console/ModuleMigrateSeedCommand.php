<?php

namespace LaravelDevkit\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use LaravelDevkit\Devkit\Repos\ModuleRepo;
use Symfony\Component\Console\Input\InputOption;

class ModuleMigrateSeedCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $name = 'kit:m-seed';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command to seed module migration';
    protected $module;

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function handle()
    {
        $modules = array_collapse(Cache::get('modules'));

        if (! $this->option('module')){
            $userOption = $this->choice('Choose module(Press enter to Seed all)', array_merge($modules, ['all' => 'All']), "all");

            if ($userOption === 'all'){
                $this->seed($modules, true);

            } else{
                $this->module = $modules[$userOption];

                $this->seed($modules, false);
            }
        }else{
            if (! ModuleRepo::moduleExists($this->option('module'), false)){
                $this->error("Module does not exist");
                return false;
            }

            $this->module = $this->option('module');

            $this->seed($modules, false);
        }

    }

    protected function seed($modules, $migrateAll = false){
        if ($migrateAll){
            $this->executeSeeding($modules);
        } else{
            $this->executeSeeding([$this->module]);
        }
    }

    protected function executeSeeding($modules)
    {
        foreach ($modules as $module){
            $params = [];

            $modulesNamespace = config('laravel-devkit.namespace.module');
            $moduleRootSeeder = 'DatabaseSeeder';
            $rootSeederClass = $modulesNamespace.$module.'\Database\Seeds\\'.$moduleRootSeeder;

            if (class_exists($rootSeederClass)){
                $params['--class'] = $rootSeederClass;

                if ($option = $this->option('database')) {
                    $params['--database'] = $option;
                }

                if ($option = $this->option('force')) {
                    $params['--force'] = $option;
                }

                $this->call('db:seed', $params);
            }

        }
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['database', null, InputOption::VALUE_OPTIONAL, 'The database connection to use.'],
            ['module', null, InputOption::VALUE_OPTIONAL, 'The database connection to use.'],
            ['force', null, InputOption::VALUE_NONE, 'Force the operation to run while in production.'],
        ];
    }
}
