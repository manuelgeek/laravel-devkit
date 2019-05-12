<?php

namespace LaravelDevkit\Console;

use Illuminate\Console\Command;
use Illuminate\Console\ConfirmableTrait;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use LaravelDevkit\Devkit\Repos\ModuleRepo;
use Symfony\Component\Console\Input\InputOption;

class ModuleMigrateRefreshCommand extends Command
{
    use ConfirmableTrait;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $name = 'kit:refresh';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command to refresh module migration';
    protected $module;
    protected $migrator;

    public function __construct()
    {
        parent::__construct();
        $this->migrator = app('migrator');
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function handle()
    {
        if (! $this->confirmToProceed()){
            return false;
        }

        $modules = array_collapse(Cache::get('modules'));

        if (! $this->option('module')){
            $userOption = $this->choice('Choose module', array_merge($modules, ['all' => 'All']), "all");

            if ($userOption === 'all'){
                $this->refresh($modules, true);

            } else{
                $this->module = $modules[$userOption];

                $this->refresh($modules, false);

            }
        }else{
            $this->module = $this->option('module');

            $this->refresh($modules, false);
        }

    }

    protected function refresh($modules, $migrateAll = false){
        if ($migrateAll){
            foreach ($modules as $module){
                $this->executeRefresh($module);
            }
        } else{
            $this->executeRefresh($this->module);
        }
    }

    protected function executeRefresh($module){

        $this->call('kit:migrate:fresh', [
            '--module' => $module,
            '--database' => $this->option('database'),
            '--force' => $this->option('force'),
            '--pretend' => $this->option('pretend')
        ]);

        $this->call('kit:migrate', [
            '--module' => $module,
            '--database' => $this->option('database'),
        ]);

        if ($this->option('seed')){
            $this->call('kit:m-seed', [
                '--module'       => $module,
                '--database' => $this->option('database'),
            ]);
        }

        $this->line("Module refreshed");
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
            ['pretend', null, InputOption::VALUE_NONE, 'Dump the SQL queries that would be run.'],
            ['seed', null, InputOption::VALUE_NONE, 'Indicates if the seed task should be re-run.'],
            ['step', null, InputOption::VALUE_NONE, 'Force the migrations to be run so they can be rolled back individually.'],
        ];
    }
}
