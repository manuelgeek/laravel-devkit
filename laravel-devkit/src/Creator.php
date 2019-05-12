<?php
/**
 * Created by PhpStorm.
 * User: marvincollins
 * Date: 1/2/19
 * Time: 9:28 PM
 */

namespace LaravelDevkit;


/**
 * @property string controller
 * @method installModule(\Illuminate\Config\Repository $retrivedModule)
 * @method createModule($modelName)
 */
class Creator
{
//    public $modules = array();
    public $classes = array();
//    public $formatters = array();

    public function addClass($newclass)
    {
        array_unshift($this->classes, $newclass);

        return $this;
    }

    public function __call($method, $attributes)
    {
        return $this->format($method, $attributes);
    }

    public function format($formatter, $attributes = [])
    {
        return call_user_func_array($this->getFormatter($formatter), $attributes);
    }

    public function getFormatter($formatter)
    {
        foreach ($this->classes as $class){
            if (method_exists($class,$formatter)){
                return [$class, $formatter];
            }
        }

        throw new \InvalidArgumentException("Formatter not found");
    }
}