<?php

namespace LechugaNegra\SettingManager\Providers;

use Illuminate\Support\ServiceProvider;

class SettingManagerProvider extends ServiceProvider
{
    /**
     * Registrar servicios del paquete, incluyendo configuración.
     *
     * @return void
     */
    public function register()
    {
        // Registrar archivo de configuración principal
        $this->mergeConfigFrom(
            __DIR__ . '/../../config/settingmanager.php',
            'settingmanager'
        );
    }

    /**
     * Realizar las configuraciones necesarias.
     *
     * @return void
     */
    public function boot()
    {
        // Publicar la configuración
        $this->publishes([
            __DIR__ . '/../../config/settingmanager.php' => config_path('settingmanager.php'),
        ], 'settingmanager-config');

        // Cargar migraciones
        $this->loadMigrationsFrom(__DIR__ . '/../Database/Migrations');

        // Registrar comandos
        if ($this->app->runningInConsole()) {
            $this->commands([
                \LechugaNegra\SettingManager\Console\Commands\CreateSetting::class,
            ]);
        }

        // Cargar rutas
        $this->loadRoutesFrom(__DIR__ . '/../Routes/api.php');
    }
}
