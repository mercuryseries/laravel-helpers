<?php

namespace MercurySeries\Helpers;

use ReflectionClass;
use View;
use Illuminate\Support\ServiceProvider;

class HelpersServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadHelpersFrom(app_path('Helpers'));
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    public static function loadHelpersFrom($directory)
    {
        $helpers = static::findAllHelpersIn($directory);

        foreach ($helpers as $helper) {
            static::registerMethods($helper);
        }
    }

    public static function findAllHelpersIn($directory)
    {
        return array_diff(scandir($directory), array('..', '.'));
    }

    public static function registerMethods($helper)
    {
        $helperClassName = substr($helper, 0, -4);
        $reflector = new ReflectionClass('App\\Helpers\\' . $helperClassName);
        $methods = $reflector->getMethods();

        foreach ($methods as $method) {
            $methodHelper = function(...$params) use ($method) {
                $method->class::{$method->name}(...$params);
            };

            View::share($method->name, $methodHelper);
        }
    }
}