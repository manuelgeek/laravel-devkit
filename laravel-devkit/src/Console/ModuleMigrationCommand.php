<?php

namespace LaravelDevkit\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class ModuleMigrationCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'kit:m-migration {name : Migration name} {--module=} {--create=} {--table=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command to make module migration';
    protected $module;

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $modules = array_collapse(Cache::get('modules'));

        if (! $this->option('module')){
            $this->module = $modules[$this->choice('Choose module', $modules, "Cancel")];
        }else{
            $this->module = $this->option('module');
        }

        $getOptions = $this->option();
        $options = [];

        array_walk($getOptions, function (&$value, $key) use(&$options){
            $options['--'.$key] = $value;
        });

        unset($options['--module']);

        $modulePath =  module_path('Database/Migrations',$this->module);
        $options['--path'] = ltrim(str_replace(realpath(base_path()), '', realpath($modulePath)));

        return $this->call('make:migration', array_merge($this->argument(), $options));
    }
}
