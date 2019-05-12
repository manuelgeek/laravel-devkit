<?php

namespace LaravelDevkit\Console;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use LaravelDevkit\Devkit\Contracts\GeneratorCommand;

class ModuleModelCommand extends GeneratorCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'kit:model {name : Model name} {--m|migration : Model migration} {--r|resource : Controller resource} {--f|factory} {--a|all} {--c|controller : Model controller} {--mod=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command to make module model';
    protected $module;
    protected $type = 'Module model';

    /**
     * Execute the console command.
     *
     * @return mixed
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function handle()
    {
        parent::handle();

        if ($this->option('migration')){
            $table = Str::plural(Str::snake(class_basename($this->argument('name'))));

            $this->call('kit:migration',[
                'name' => "create_{$table}_table",
                '--create' => $table,
                '--mod' => $this->module
            ]);
        }

        if ($this->options('controller')){
            $controller = Str::studly(class_basename($this->argument('name')));

            $this->call('kit:controller', [
                'name' => "{$controller}Controller",
                '--mod' => $this->module,
                '--resource' => $this->option('resource') ? $this->argument('name') : false,
            ]);
        }

        if ($this->options('factory')){

        }
    }

    protected function getDefaultNamespace($rootNamespace)
    {
        return module_class('Models', $this->module);
    }

    protected function getStub()
    {
        return __DIR__ . '/../../resources/stubs/model.stub';
    }
}
