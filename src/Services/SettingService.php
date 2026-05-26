<?php

namespace LechugaNegra\SettingManager\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use LechugaNegra\SettingManager\Models\Setting;

class SettingService
{
    /**
     * Obtener todos los settings activos de un módulo.
     *
     * @param string $module Módulo a consultar.
     * @return array Settings del módulo con su valor y tipo.
     */
    public function getByModule(string $module): array
    {
        return Cache::remember(
            "settingmanager.module.{$module}",
            config('settingmanager.cache_ttl', 3600),
            fn() => Setting::where('module', $module)
                ->where('is_active', true)
                ->get()
                ->mapWithKeys(fn($s) => [$s->key => [
                    'value' => $s->value,
                    'type' => $s->type,
                ]])
                ->toArray()
        );
    }

    /**
     * Obtener un setting puntual por módulo y clave.
     *
     * @param string $module Módulo del setting.
     * @param string $key Clave del setting.
     * @return array|null Setting con módulo, clave, tipo y valor, o null si no existe.
     */
    public function get(string $module, string $key): array|null
    {
        $setting = Cache::remember(
            "settingmanager.{$module}.{$key}",
            config('settingmanager.cache_ttl', 3600),
            fn() => Setting::where('module', $module)
                ->where('key', $key)
                ->where('is_active', true)
                ->first()
        );

        if (!$setting) {
            return null;
        }

        return [
            'module' => $module,
            'key' => $key,
            'type' => $setting->type,
            'value' => $setting->value,
        ];
    }

    /**
     * Actualizar uno o varios settings de un módulo.
     *
     * @param string $module Módulo al que pertenecen los settings.
     * @param array $data Array con clave 'data' conteniendo los pares key/value a actualizar.
     * @return array Settings actualizados con su valor y tipo.
     */
    public function update(string $module, array $data): array
    {
        $updated = [];

        foreach ($data['data'] as $item) {
            $setting = Setting::where('module', $module)
                ->where('key', $item['key'])
                ->first();

            if (!$setting) {
                Log::warning("SettingService.update: key [{$item['key']}] no existe en módulo [{$module}]");
                continue;
            }

            $setting->value = $item['value'];
            $setting->save();

            $this->clearCache($module, $item['key']);

            $updated[$item['key']] = [
                'value' => $setting->value,
                'type' => $setting->type,
            ];
        }

        return $updated;
    }

    /**
     * Limpiar caché de un módulo o de un setting puntual.
     *
     * @param string $module Módulo a limpiar.
     * @param string|null $key Clave específica a limpiar (opcional).
     * @return void
     */
    public function clearCache(string $module, ?string $key = null): void
    {
        if ($key) {
            Cache::forget("settingmanager.{$module}.{$key}");
        }
        Cache::forget("settingmanager.module.{$module}");
    }
}
