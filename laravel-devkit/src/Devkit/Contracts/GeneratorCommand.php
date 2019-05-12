<?php
/**
 * Created by PhpStorm.
 * User: marvincollins
 * Date: 1/12/19
 * Time: 7:43 PM
 */

namespace LaravelDevkit\Devkit\Contracts;

use Illuminate\Console\GeneratorCommand as LaravelGeneratorCommand;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class GeneratorCommand extends LaravelGeneratorCommand
{

    public function handle()
    {
        $modules = array_collapse(Cache::get('modules'));

        if (! $this->option('module')){
            $this->module = $modules[$this->choice('Choose module', $modules, "Cancel")];
        }else{
            $this->module = $this->option('module');
        }

        parent::handle();

    }

    protected function getPath($name)
    {
        $key = array_search(strtolower($this->module), explode('\\', strtolower($name)));

        if ($key === false) {
            $newPath = str_replace('\\', '/', $name);
        } else {
            $newPath = implode('/', array_slice(explode('\\', $name), $key + 1));
        }

        return module_path("$newPath.php",$this->module);
    }

    protected function qualifyClass($name)
    {
        $name = ltrim($name,'\\/');

        $rootNamespace = config('laravel-devkit.namespace.module');

        if (Str::startsWith($name, $rootNamespace)) {
            return $name;
        }

        $name = str_replace('/', '\\', $name);

        return $this->qualifyClass(
            $this->getDefaultNamespace(trim($rootNamespace, '\\')).'\\'.$name
        );
    }

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        // TODO: Implement getStub() method.
    }
}