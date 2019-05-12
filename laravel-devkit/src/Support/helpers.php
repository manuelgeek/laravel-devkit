<?php
/**
 * Created by PhpStorm.
 * User: marvincollins
 * Date: 1/8/19
 * Time: 11:42 PM
 */

if (!function_exists('module_path')){
    function module_path($file, $moduleName = null){
        $modulesPath = config("laravel-devkit.locations.modules");

        $filePath = $file ? '/' . ltrim($file, '/') : '';

        if (is_null($moduleName)) {
            if (empty($file)) {
                return $modulesPath;
            }

            return $modulesPath . $filePath;
        }

        if (!(new \Illuminate\Filesystem\Filesystem())->isDirectory($modulesPath."/".$moduleName)) {
            \LaravelDevkit\Devkit\Repos\ModuleRepo::cleanModules($moduleName);
            throw new \InvalidArgumentException("Model does not exist");
        }

        return $modulesPath . '/' . $moduleName . $filePath;
    }
}

if (! function_exists('module_class')){
    function module_class($class, $moduleName)
    {
        $modulesPath = config("laravel-devkit.locations.modules");

        if (!(new \Illuminate\Filesystem\Filesystem())->isDirectory($modulesPath."/".$moduleName)) {
            throw new \InvalidArgumentException("Model does not exist");
        }

        $namespace = config("laravel-devkit.namespace.module") . $moduleName;

        return "$namespace\\$class";
    }
}