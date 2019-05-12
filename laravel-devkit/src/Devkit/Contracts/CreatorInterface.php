<?php
/**
 * Created by PhpStorm.
 * User: marvincollins
 * Date: 1/2/19
 * Time: 9:50 PM
 */

namespace LaravelDevkit\Devkit\Contracts;


use LaravelDevkit\Creator;

interface CreatorInterface
{
    public static function build() : Creator ;
}