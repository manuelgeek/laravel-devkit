<?php
/**
 * Created by PhpStorm.
 * User: marvincollins
 * Date: 1/2/19
 * Time: 9:47 PM
 */

namespace LaravelDevkit\Devkit\Providers;


use Illuminate\Support\Collection;
use LaravelDevkit\Devkit\Contracts\CreatorAbstract;

class Module extends CreatorAbstract
{
    /**
     * @param array $module
     */
    public function installModule(Collection $module)
    {
        return parent::build()->import($module);
    }

    /**
     * @param array $container
     * @return mixed
     */
    public function createModule(array $container)
    {
        return parent::build()->create($container);
    }

    //TODO: implement checks for theme, name,
}