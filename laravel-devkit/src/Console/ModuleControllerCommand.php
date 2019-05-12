<?php

namespace LaravelDevkit\Console;

use LaravelDevkit\Devkit\Contracts\GeneratorCommand;

class ModuleControllerCommand extends GeneratorCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'kit:controller {name : Controller name} {--r|resource : Resource controller} {--mod=} {--force}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command to make controller';
    protected $module;
    protected $type = 'Module controller';

    protected function getDefaultNamespace($rootNamespace)
    {
        return module_class('Http\\Controllers', $this->module);
    }

    protected function getStub()
    {
        if ($this->option('resource') && $this->option('resource') != null) {
            return __DIR__ . '/../../resources/stubs/controller.resource.stub';
        }

        return __DIR__ . '/../../resources/stubs/controller.stub';
    }
}
