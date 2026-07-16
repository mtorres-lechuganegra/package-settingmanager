<?php

namespace LechugaNegra\SettingManager\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use LechugaNegra\SettingManager\Models\Setting;

class SettingService
{
    /**
     * Obtener un setting puntual por módulo y clave.
     *
     * @param string $module Módulo del setting.
     * @param string $key Clave del setting.
     * @param string $group Grupo del setting.
     * @param bool $includeLocked Incluid registros bloqueados.
     * @return array|null Setting con módulo, clave, tipo y valor, o null si no existe.
     */
    public function get(string $module, string $key, string $group = '', bool $includeLocked = false, bool $onlyActive = true): array|null
    {
        $setting = Cache::remember(
            "settingmanager.{$module}.{$group}.{$key}",
            config('settingmanager.cache_ttl', 3600),
            fn() => Setting::where('module', $module)
                ->where(fn($q) => $q->where('group', $group ?? '')->orWhereNull('group'))
                ->where('key', $key)
                ->when($onlyActive, fn($q) => $q->where('is_active', true))
                ->when(!$includeLocked, fn($q) => $q->where('is_locked', false))
                ->first()
        );

        if (!$setting) {
            return null;
        }

        return [
            'module' => $module,
            'group' => $setting->group,
            'key' => $key,
            'type' => $setting->type,
            'value' => $setting->value,
            'is_active' => $setting->is_active,
        ];
    }

    /**
     * Obtener todos los settings activos de un módulo.
     *
     * @param string $module Módulo a consultar.
     * @param bool $includeLocked Incluid registros bloqueados.
     * @return array Settings del módulo con su valor y tipo.
     */
    public function getByModule(string $module, bool $includeLocked = false, bool $onlyActive = true): array
    {
        return Cache::remember(
            "settingmanager.module.{$module}",
            config('settingmanager.cache_ttl', 3600),
            fn() => Setting::where('module', $module)
                ->when($onlyActive, fn($q) => $q->where('is_active', true))
                ->when(!$includeLocked, fn($q) => $q->where('is_locked', false))
                ->get()
                ->mapWithKeys(fn($s) => [$s->key => [
                    'group' => $s->group,
                    'value' => $s->value,
                    'type' => $s->type,
                    'is_active' => $s->is_active,
                ]])
                ->toArray()
        );
    }

    /**
     * Actualizar unoa variable setting de un módulo.
     *
     * @param string $module Módulo del setting.
     * @param string $key Clave del setting.
     * @param string $group Grupo del setting.
     * @param bool $includeLocked Incluid registros bloqueados.
     * @return array|null Setting con módulo, clave, tipo y valor, o null si no existe.
     */
    public function update(string $module, string $key, string $group = '', mixed $value = null, ?bool $isActive = null, bool $includeLocked = false): array|null
    {
        $setting = Setting::where('module', $module)
            ->where('key', $key)
            ->when(
                $group !== '',
                fn($q) => $q->where('group', $group),
                fn($q) => $q->where(fn($q) => $q->where('group', '')->orWhereNull('group'))
            )
            ->when(!$includeLocked, fn($q) => $q->where('is_locked', false))
            ->first();

        if (!$setting) {
            Log::warning("SettingService.update: key [{$key}] no existe en módulo [{$module}]");
            return null;
        }

        $setting->value = $value;

        if ($isActive !== null) {
            $setting->is_active = $isActive;
        }

        $setting->save();

        $this->clearCache($module, $key, $group);

        return [
            'group' => $setting->group,
            'value' => $setting->value,
            'type' => $setting->type,
            'is_active' => $setting->is_active,
        ];
    }

    /**
     * Actualizar unoa variable setting de un módulo.
     *
     * @param string $module Módulo al que pertenecen los settings.
     * @param array $data Array con clave 'data' conteniendo los pares key/value a actualizar.
     * @param bool $includeLocked Incluid registros bloqueados.
     * @return array Settings actualizados con su valor y tipo.
     */
    public function updateByModule(string $module, array $data, bool $includeLocked = false): array
    {
        $updated = [];

        foreach ($data['data'] as $item) {
            $setting = Setting::where('module', $module)
                ->where('key', $item['key'])
                ->when(
                    isset($item['group']) && $item['group'] !== '',
                    fn($q) => $q->where('group', $item['group']),
                    fn($q) => $q->where(fn($q) => $q->where('group', '')->orWhereNull('group'))
                )
                ->when(!$includeLocked, fn($q) => $q->where('is_locked', false))
                ->first();

            if (!$setting) {
                Log::warning("SettingService.update: key [{$item['key']}] no existe en módulo [{$module}]");
                continue;
            }

            if (array_key_exists('is_active', $item) && $item['is_active'] !== null) {
                $setting->is_active = $item['is_active'];
            }

            $setting->value = $item['value'];
            $setting->save();

            $this->clearCache($module, $item['key'], $item['group'] ?? '');

            $updated[$item['key']] = [
                'group' => $setting->group,
                'value' => $setting->value,
                'type' => $setting->type,
                'is_active' => $setting->is_active,
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
    public function clearCache(string $module, ?string $key = null, string $group = ''): void
    {
        if ($key) {
            Cache::forget("settingmanager.{$module}.{$group}.{$key}");
        }
        Cache::forget("settingmanager.module.{$module}");
    }
}
