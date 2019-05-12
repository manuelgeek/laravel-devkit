<?php
/**
 * Created by PhpStorm.
 * User: marvincollins
 * Date: 1/2/19
 * Time: 9:49 PM
 */

namespace LaravelDevkit\Devkit\Contracts;


use LaravelDevkit\Builder;
use LaravelDevkit\Creator;
use Illuminate\Filesystem\Filesystem;

abstract class CreatorAbstract implements CreatorInterface
{
    public $locations;
    public $files;

    public function __construct(Filesystem $files)
    {
        $this->locations = config('laravel-devkit.locations');
        $this->files = $files;
    }

    /**
     * @return Builder|Creator
     */
    public static function build() : Creator
    {
        return Builder::build();
    }

    public function createDir($path)
    {
        if (!$this->files->isDirectory($path)){
            $this->files->makeDirectory($path, 0755,true);
        }

        return $path;
    }
}