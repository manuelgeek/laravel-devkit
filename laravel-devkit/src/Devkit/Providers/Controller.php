<?php
/**
 * Created by PhpStorm.
 * User: marvincollins
 * Date: 1/2/19
 * Time: 9:47 PM
 */

namespace LaravelDevkit\Devkit\Providers;


use LaravelDevkit\Devkit\Contracts\CreatorAbstract;

class Controller extends CreatorAbstract
{

    public static function getStub()
    {
        return __DIR__.'/../../../laravel-starterkit/resources/stubs/Controller.stub';
    }

    public static function getModulePath()
    {
        // TODO: Implement getModulePath() method.
    }
}