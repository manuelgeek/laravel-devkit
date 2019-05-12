<?php

namespace LaravelDevkit\Console;

use Illuminate\Support\Facades\Cache;
use LaravelDevkit\Devkit\Contracts\GeneratorCommand;

class ModuleRequestCommand extends GeneratorCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'kit:request {name : Request name}  {--mod}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command to make module request';
    protected $module;
    protected $type = 'Module request';

    protected function getDefaultNamespace($rootNamespace)
    {
        return module_class('Http\\Requests', $this->module);
    }

    protected function getStub()
    {
        return __DIR__ . '/../../resources/stubs/request.stub';
    }
}
