<?php
/**
 * Created by PhpStorm.
 * User: marvincollins
 * Date: 1/1/19
 * Time: 9:51 PM
 */

namespace LaravelDevkit\Devkit\Repos;


use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;

class ModuleRepo
{
    /**
     * @var
     */
    public $module;
    public $modulePath;

    /**
     * @return \Illuminate\Config\Repository|mixed
     */
    public static function getModules()
    {
        return config('laravel-devkit-modules.modules');
    }

    public static function module()
    {
        return new self;
    }

    public static function getModuleSource($moduleName)
    {
        return config('laravel-devkit-modules.modules.'.str_slug(str_replace(" ","",$moduleName)));
    }

    public static function getModuleManifest($modulePath)
    {
        $manifestContent = File::get($modulePath.'/module.json');
        $validate = @json_decode($manifestContent, true);

        if (json_last_error() === JSON_ERROR_NONE) {
            $collection = collect(json_decode($manifestContent, true));

            return $collection;
        }

        throw new \InvalidArgumentException('Module manifest file was not properly formatted. Check for formatting issues and try again.');

    }

    public static function addModule(array $module)
    {
        if (!Cache::has('modules')){
            Cache::forever('modules',[$module]);
        }
        else{
            $modules = Cache::get('modules');
            array_push($modules, $module);
            Cache::forget('modules');
            Cache::forever('modules',$modules);
        }

        foreach ($module as $moduleName){
            self::writeModuleConfig($moduleName);
        }

        return true;
    }

    public static function cleanModules($module, $is_slug = false)
    {
        $modules = Cache::get('modules');

        if ($is_slug){
            if(ModuleRepo::moduleExists($module)){
                foreach ($modules as $key => $moduleItems){
                    foreach ($moduleItems as $innerKey => $moduleName){
                        if ($module == $innerKey){
                            unset($modules[$key]);
                        }
                    }
                }
            }
        }
        else{
            foreach ($modules as $key => $moduleItems){
                foreach ($moduleItems as $innerKey => $moduleName){
                    if ($module == $moduleName){
                        unset($modules[$key]);
                    }
                }
            }
        }

        Cache::forget('modules');
        Cache::forever('modules', $modules);
    }

    public static function writeModuleConfig($moduleName, $search = null, $replace = null)
    {
        $search = $search ?: "//installed-modules";
        $replace = $replace ?: "'".str_slug($moduleName)."'=>'".$moduleName."',".PHP_EOL."//installed-modules".PHP_EOL;
        $moduleConfig = file_get_contents(config_path('laravel-devkit-modules.php'));

        file_put_contents(config_path('laravel-devkit-modules.php'), str_replace(
            $search,
            $replace,
            $moduleConfig
        ));
    }

    public static function moduleExists($slug, $is_slug = true)
    {
        $modules = array_collapse(Cache::get('modules'));

        if ($modules && in_array($slug, $modules) && !$is_slug){
            return true;
        }

        if ($modules && array_key_exists($slug,$modules) && $is_slug){
            return true;
        }

        return false;
    }
}