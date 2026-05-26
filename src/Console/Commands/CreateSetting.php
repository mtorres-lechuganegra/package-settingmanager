<?php

namespace LechugaNegra\SettingManager\Console\Commands;

use Illuminate\Console\Command;
use LechugaNegra\SettingManager\Models\Setting;

class CreateSetting extends Command
{
    protected $signature = 'settings:create
                            {module : Módulo al que pertenece}
                            {key : Clave única del setting}
                            {type : Tipo de dato (string|integer|float|boolean|json|array)}
                            {--value= : Valor inicial}
                            {--description= : Descripción del setting}
                            {--inactive : Crear el setting como inactivo}';

    protected $description = 'Crea un nuevo setting de configuración';

    public function handle(): int
    {
        $setting = Setting::firstOrCreate(
            [
                'module' => $this->argument('module'),
                'key' => $this->argument('key'),
            ],
            [
                'type' => $this->argument('type'),
                'value' => $this->option('value'),
                'description' => $this->option('description'),
                'is_active' => !$this->option('inactive'),
            ]
        );

        if ($setting->wasRecentlyCreated) {
            $this->info("Setting [{$this->argument('module')}.{$this->argument('key')}] creado correctamente.");
        } else {
            $this->warn("Setting [{$this->argument('module')}.{$this->argument('key')}] ya existe, no se modificó.");
        }

        return Command::SUCCESS;
    }
}
