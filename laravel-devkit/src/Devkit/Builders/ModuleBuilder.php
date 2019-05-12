<?php
/**
 * Created by PhpStorm.
 * User: marvincollins
 * Date: 1/3/19
 * Time: 7:29 PM
 */

namespace LaravelDevkit\Devkit\Builders;

use Illuminate\Support\Facades\Artisan;
use LaravelDevkit\Devkit\Contracts\CreatorAbstract;
use LaravelDevkit\Devkit\Repos\ModuleRepo;


class ModuleBuilder extends CreatorAbstract
{
    public $moduleName;
    public $container = [];

    public function import($module)
    {
        $this->container['module_name'] = studly_case(str_replace("/","_", $module['name']));
        $this->container['base'] = config('laravel-devkit.locations.modules').'/'.$this->container['module_name'];
        $this->container['source'] = $module['source'];
        $this->container['name'] = $module['name'];
        $this->container['module_slug'] = $module['module_slug'];

        if (ModuleRepo::moduleExists($this->container['module_slug'])){

            return [
                'type' => 'error',
                'message' => "Model with the same name already exist by different vendor"
            ];

        }

        if ($this->files->isDirectory($this->container['base'])){

            return [
                'type' => 'error',
                'message' => $this->container['name']." module is already installed"
            ];
        }

        ModuleRepo::cleanModules($this->container['module_slug'], true);

        $this->files->makeDirectory($this->container['base'],0755, true);

        $moduleFiles = $this->files->allFiles($this->container['source'], true);

        foreach ($moduleFiles as $file){
            $subDir = $file->getRelativePathname();

            $fileDir = $this->container['base'].'/'.$subDir;

            $dir = dirname($fileDir);

            parent::createDir($dir);

            $fileContent = $file->getContents();

            $this->files->put($fileDir,$fileContent);

        }

        ModuleRepo::addModule([$this->container['module_slug'] => $this->container['module_name']]);

        return [
            'type' => 'info',
            'message' => $this->container['name']." module installed successfully"
        ];

    }

    /**
     * @param array $container
     */
    public function create(array $container)
    {
        $this->container = $container;

        if (ModuleRepo::moduleExists($this->container['module_slug'])){

            return [
                'type' => 'error',
                'message' => "Model with the same name already exist by different vendor"
            ];
        }

        $this->container['locations'] = config('laravel-devkit.locations.modules');
        parent::createDir($this->container['locations']);

        if ($this->files->isDirectory($this->container['locations']."/".$this->container['base'])){
            return [
                'type' => 'error',
                'message' => $this->container['name']." module is already created"
            ];
        }

        parent::createDir($this->container['locations']."/".$this->container['base']);

        $stubSource = __DIR__.'/../../../resources/stubs/module';

        $stubFiles = $this->files->allFiles($stubSource, true);

        foreach ($stubFiles as $file){
            $content = $this->replacePlaceHolders($file->getContents());
            $subDir = $file->getRelativePathname();

            $fileDir = config('laravel-devkit.locations.modules').'/'.$this->container['base'].'/'.$subDir;

            $dir = dirname($fileDir);

            parent::createDir($dir);

            $this->files->put($fileDir,$content);

        }

        Artisan::call('kit:m-seeder',
            [
                'name' => 'DatabaseSeeder',
                '--module' => $this->container['base']
            ]
        );

        ModuleRepo::addModule([$this->container['module_slug'] => $this->container['base']]);

        return [
            'type' => 'info',
            'message' => $this->container['name']." modules created successfully"
        ];
    }

    public function replacePlaceHolders($content)
    {

        $find = [
            'DummyBase',
            'DummyNamespace',
            'DummyName',
            'DummySlug',
            'DummyVersion',
            'DummyDescription',
            'DummyLocation',
            'DummyProvider',
            'DummyDefault',
            'DummySource',
            'DummyLicense',
            'DummyVendor',
            'DummyEmail',

            'ConfigMapping',
            'DatabaseFactoriesMapping',
            'DatabaseMigrationsMapping',
            'DatabaseSeedsMapping',
            'HttpControllersMapping',
            'HttpMiddlewareMapping',
            'ProvidersMapping',
            'ResourcesLangMapping',
            'ResourcesViewsMapping',
            'RoutesMapping',
        ];

        $replace = [
            $this->container['base'],
            $this->container['namespace'],
            $this->container['name'],
            str_replace("_","-",$this->container['module_slug']),
            $this->container['version'],
            $this->container['description'],
            config('laravel-devkit.app-location'),
            $this->container['provider'],
            'default',
            $this->container['source'],
            $this->container['license'],
            $this->container['vendor'],
            $this->container['email'],

            'Config',
            'Database/Factories',
            'Database/Migrations',
            'Database/Seeds',
            'Http/Controllers',
            'Http/Middleware',
            'Providers',
            'Resources/Lang',
            'Resources/Views',
            'Routes'
        ];

        return str_replace($find, $replace, $content);

    }
}