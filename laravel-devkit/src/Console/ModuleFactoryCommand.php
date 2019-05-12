<?php

namespace LaravelDevkit\Console;

use LaravelDevkit\Devkit\Contracts\GeneratorCommand;

class ModuleFactoryCommand extends GeneratorCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'kit:m-factory {name : Factory name} {--model= : Model} {--module=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command to make module factory';
    protected $module;
    protected $type = 'Module factory';

    protected function getDefaultNamespace($rootNamespace)
    {
        return module_class('Database\\Factories', $this->module);
    }

    protected function getStub()
    {
        return __DIR__ . '/../../resources/stubs/factory.stub';
    }

    protected function buildClass($name)
    {
        $model = $this->option('model')
            ? $this->option('model')
            : 'Model';

        return str_replace(
            ['DummyModel','DummyNamespace'], [$model,module_class('Models',$this->module)], parent::buildClass($name)
        );
    }

    protected function getNamespace($name)
    {
        return module_class("Models", $this->module);
    }
}
