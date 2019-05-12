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
    protected $signature = 'kit:m-model {name : Model name} {--migration : Model migration} {--module}';

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

            $this->call('kit:m-migration',[
                'name' => "create_{$table}_table",
                '--create' => $table,
                '--module' => $this->module
            ]);
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
