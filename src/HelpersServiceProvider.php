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
        $this->publishes([
            __DIR__ . '/../config/helpers.php' => config_path('helpers.php'),
        ], 'config');

        $helpersDirectory = config('helpers.helpers_path');

        if (file_exists($helpersDirectory) && is_dir($helpersDirectory)) {
            $this->loadHelpersFrom($helpersDirectory);
        }
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/helpers.php', 'helpers');
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
        $helperClassFQN = static::buildClassFQN($helper);

        $reflector = new ReflectionClass($helperClassFQN);

        $methods = $reflector->getMethods();

        foreach ($methods as $method) {
            $methodHelper = function(...$params) use ($method) {
                return $method->class::{$method->name}(...$params);
            };

            View::share($method->name, $methodHelper);
        }
    }

    public static function buildClassFQN($helper)
    {
        $helperClassName = substr($helper, 0, -4); // Remove .php at the end of the file name
        return config('helpers.helpers_base_namespace') . $helperClassName;
    }
}