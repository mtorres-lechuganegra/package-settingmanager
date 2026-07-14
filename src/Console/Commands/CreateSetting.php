<?php

namespace LechugaNegra\SettingManager\Console\Commands;

use Illuminate\Console\Command;
use LechugaNegra\SettingManager\Models\Setting;

class CreateSetting extends Command
{
    protected $signature = 'settings:create
                            {module : Módulo al que pertenece}
                            {key : Clave única del setting}
                            {type : Tipo de dato (string|integer|float|boolean|json|array|encrypted)}
                            {--group= : Grupo del setting (opcional)}
                            {--value= : Valor inicial}
                            {--description= : Descripción del setting}
                            {--inactive : Crear el setting como inactivo}
                            {--locked : Bloquear el setting para que no sea modificable por CRUD}';

    protected $description = 'Crea un nuevo setting de configuración';

    public function handle(): int
    {
        $module = $this->argument('module');
        $key    = $this->argument('key');
        $group  = $this->option('group');

        $setting = Setting::firstOrCreate(
            [
                'module' => $module,
                'group'  => $group,
                'key'    => $key,
            ],
            [
                'type'        => $this->argument('type'),
                'value'       => $this->option('value'),
                'description' => $this->option('description'),
                'is_active'   => !$this->option('inactive'),
                'is_locked'   => (bool) $this->option('locked'),
            ]
        );

        $name = $group
            ? "{$module}.{$group}.{$key}"
            : "{$module}.{$key}";

        if ($setting->wasRecentlyCreated) {
            $this->info("Setting [{$name}] creado correctamente.");
        } else {
            $this->warn("Setting [{$name}] ya existe, no se modificó.");
        }

        return Command::SUCCESS;
    }
}
