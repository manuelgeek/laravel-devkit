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

class ModuleMigrateFreshCommand extends Command
{
    use ConfirmableTrait;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $name = 'kit:m-migrate:fresh';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command to fresh module migration';
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
                $this->fresh($modules, true);

            } else{
                $this->module = $modules[$userOption];

                $this->fresh($modules, false);

            }
        }else{
            $this->module = $this->option('module');

            $this->fresh($modules, false);
        }

    }

    protected function fresh($modules, $migrateAll = false){
        if ($migrateAll){
            $this->executeFresh($modules);
        } else{
            $this->executeFresh([$this->module]);
        }
    }

    protected function executeFresh($modules){

            $migrationPaths = $this->getMigrationPaths($modules);
            $getMigrationfiles = $this->migrator->setOutput($this->output)->getMigrationFiles($migrationPaths);

            $migrations = array_reverse($this->migrator->getRepository()->getRan());

            if (count($migrations) == 0){
                $this->line("Nothing to rollback");
            } else{
                $this->migrator->requireFiles($getMigrationfiles);

                foreach ($migrations as $migration){
                    if (! array_key_exists($migration, $getMigrationfiles)){
                        continue;
                    } else{
                        $this->runDown($getMigrationfiles[$migration], (object) ["migration" => $migration]);
                    }
                }
            }
    }

    protected function runDown($migrationFile, $migration){
        $migrationFilename = $this->migrator->getMigrationName($migrationFile);

        $migrationFileInstance = $this->migrator->resolve($migrationFilename);

        $migrationFileInstance->down();

        $this->migrator->getRepository()->delete($migration);

        $this->info("Rolledback: ".$migrationFilename);
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
