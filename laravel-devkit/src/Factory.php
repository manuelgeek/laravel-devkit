<?php
/**
 * Created by PhpStorm.
 * User: marvincollins
 * Date: 1/2/19
 * Time: 9:27 PM
 */

namespace LaravelDevkit;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\File;

class Factory
{
    public static $defaultProviders = ['Module','Controller'];

    public static function iWantTo()
    {
        $creator = new Creator();

        foreach (self::$defaultProviders as $providerClassname){
            $providerClassname = self::getProviderClassname($providerClassname);
            $creator->addClass(new $providerClassname(new Filesystem()));
        }

        return $creator;
    }

    /**
     * @param $provider
     * @return string
     */
    public static function getProviderClassname($provider)
    {
        $providerClass = __NAMESPACE__.'\Devkit\\'.sprintf('Providers\%s',$provider);

        if (class_exists($providerClass)){
            return $providerClass;
        }

        throw new \InvalidArgumentException($providerClass. " is unavailable");
    }

    public static function displayThankYou()
    {
        return (new Filesystem())->get(__DIR__.'/../resources/stubs/console/console-header.stub');
    }

}