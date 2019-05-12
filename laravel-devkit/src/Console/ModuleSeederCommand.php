<?php

namespace LaravelDevkit\Console;

use LaravelDevkit\Devkit\Contracts\GeneratorCommand;

class ModuleSeederCommand extends GeneratorCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'kit:seeder {name : Seed name} {--mod=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command to make module seed';
    protected $module;
    protected $type = 'Module seeder';

    protected function getPath($name)
    {
        return module_path("Database/Seeds/$name.php",$this->module);
    }

    protected function qualifyClass($name)
    {
        return $name;
    }

    protected function getNamespace($name)
    {
        return module_class('Database\Seeds', $this->module);
    }

    protected function getStub()
    {
        return __DIR__ . '/../../resources/stubs/seeder.stub';
    }
}
