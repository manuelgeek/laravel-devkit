<?php

namespace LaravelDevkit\Console;

use Illuminate\Console\Command;
use Illuminate\Console\ConfirmableTrait;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use LaravelDevkit\Devkit\Repos\ModuleRepo;
use Symfony\Component\Console\Input\InputOption;

class ModuleMigrateCommand extends Command
{
    use ConfirmableTrait;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $name = 'kit:migrate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command to make module model';
    protected $module;
    protected $type = 'Module model';
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
        $this->prepareDatabase();

        $modules = array_collapse(Cache::get('modules'));

        if (! $this->option('module')){
            $userOption = $this->choice('Choose module', array_merge($modules, ['all' => 'All']), "all");

            if ($userOption === 'all'){
                $this->migrate($modules, true);

            } else{
                $this->module = $modules[$userOption];

                $this->migrate($modules, false);

            }
        }else{
            $this->module = $this->option('module');

            $this->migrate($modules, false);
        }

    }

    protected function migrate($modules, $migrateAll = false){
        if ($migrateAll){
            foreach ($modules as $module){
                $this->executeMigrations($module);
            }
        } else{
            $this->executeMigrations($this->module);
        }
    }

    protected function executeMigrations($module){

        if (ModuleRepo::moduleExists($module, false)){
            $pretend = Arr::get($this->option(), 'pretend', false);
            $step = Arr::get($this->option(), 'step', false);
            $path = $this->getMigrationPath($module);

            $this->migrator->setOutput($this->output)->run($path,['pretend' => $pretend, 'step' => $step]);

            if ($this->option('seed')) {
                $this->call('kit:m-seed', ['--module' => $module, '--force' => true]);
            }

            $this->line("Migration completed");
        }
    }

    protected function getMigrationPath($module){
        return module_path('Database/Migrations', $module);
    }

    protected function prepareDatabase(){

        $this->migrator->setConnection($this->option('database'));

        if (!$this->migrator->repositoryExists()){
            $options = ['--database' => $this->option('database')];

            $this->call('migrate:install', $options);
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
            ['pretend', null, InputOption::VALUE_NONE, 'Dump the SQL queries that would be run.'],
            ['seed', null, InputOption::VALUE_NONE, 'Indicates if the seed task should be re-run.'],
            ['step', null, InputOption::VALUE_NONE, 'Force the migrations to be run so they can be rolled back individually.'],
        ];
    }
}
