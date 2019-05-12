<?php

namespace LaravelDevkit\Console;

use Illuminate\Console\Command;
use Illuminate\Console\ConfirmableTrait;
use Illuminate\Database\Migrations\Migrator;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use LaravelDevkit\Devkit\Repos\ModuleRepo;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class ModuleMigrateRollbackCommand extends Command
{
    use ConfirmableTrait;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $name = 'kit:m-rollback';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command to rollback module migration';
    protected $module;
    protected $migrator;

    public function __construct(Migrator $migrator)
    {
        parent::__construct();
        $this->migrator = $migrator;
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

        $this->prepareDatabase();

        $modules = array_collapse(Cache::get('modules'));

        if (! $this->option('module')){
            $userOption = $this->choice('Choose module', array_merge($modules, ['all' => 'All']), "all");

            if ($userOption === 'all'){
                $this->rollback($modules, true);

            } else{
                $this->module = $modules[$userOption];

                $this->rollback($modules, false);

            }
        }else{
            $this->module = $this->option('module');

            $this->rollback($modules, false);
        }

    }

    protected function rollback($modules, $migrateAll = false){
        if ($migrateAll){
            $this->executeRollback($modules);
        } else{
            $this->executeRollback([$this->module]);
        }
    }

    protected function executeRollback($modules){

            $migrationPaths = $this->getMigrationPaths($modules);
            $this->migrator->setOutput($this->output)->rollback(
                $migrationPaths,[ 'pretend' => $this->option('pretend'), 'step' => (int)$this->option('step')]);

            $this->line("Modules rolled back");
    }

    protected function getMigrationPaths($modules){
        $migrationPaths = [];

        foreach ($modules as $module){
            if (ModuleRepo::moduleExists($module, false)){
                $migrationPaths[] = module_path('Database/Migrations', $module);
            }
        }

        return $migrationPaths;
    }

    protected function prepareDatabase(){

        $this->migrator->setConnection($this->option('database'));

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
