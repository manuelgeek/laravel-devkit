<?php
/**
 * Created by PhpStorm.
 * User: marvincollins
 * Date: 1/3/19
 * Time: 6:44 PM
 */

namespace LaravelDevkit;


use Illuminate\Filesystem\Filesystem;

class Builder
{

    public static $defaultBuilders = ['ModuleBuilder','ModuleControllerBuilder'];

    public static function build()
    {
        $creator = new Creator();

        foreach (self::$defaultBuilders as $builder){
            $builderClassname = self::getBuilderClassname($builder);
            $creator->addClass(new $builderClassname(new Filesystem()));
        }

        return $creator ;
    }

    public static function getBuilderClassname($builder)
    {
        $providerClass = "LaravelDevkit\Devkit\Builders\ModuleBuilder";

        if (class_exists($providerClass)){
            return $providerClass;
        }

        self::throwNewException($providerClass. " is unavailable");
    }

    public static function throwNewException($message)
    {
        throw new \InvalidArgumentException($message);
    }

    //TODO:download module as zip and unzipp to install
}