<?php

namespace LechugaNegra\SettingManager\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use LechugaNegra\SettingManager\Http\Requests\GetSettingByModuleRequest;
use LechugaNegra\SettingManager\Http\Requests\GetSettingRequest;
use LechugaNegra\SettingManager\Http\Requests\UpdateSettingByModuleRequest;
use LechugaNegra\SettingManager\Http\Requests\UpdateSettingRequest;
use LechugaNegra\SettingManager\Services\SettingService;

class SettingController extends Controller
{
    protected $settingService;

    public function __construct(SettingService $settingService)
    {
        $this->settingService = $settingService;
    }

    /**
     * Obtener un setting puntual por módulo y clave.
     *
     * @param GetSettingRequest $request Datos validados con el grupo opcional.
     * @param string $module Módulo del setting.
     * @param string $key Clave del setting.
     * @return JsonResponse Setting encontrado (200), no encontrado (404) o error (500).
     */
    public function get(GetSettingRequest $request, string $module, string $key): JsonResponse
    {
        try {
            $group = $request->validated()['group'] ?? '';
            $onlyActive = $request->validated()['only_active'] ?? true;
            $setting = $this->settingService->get($module, $key, $group, false, $onlyActive);

            if (!$setting) {
                return response()->json(['error' => 'Setting not found'], 404);
            }

            return response()->json($setting, 200);
        } catch (\Exception $e) {
            Log::error("SettingController.get: {$e->getMessage()}", ['module' => $module, 'key' => $key]);
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Obtener todos los settings activos de un módulo.
     *
     * @param GetSettingByModuleRequest $request Datos validados con el grupo opcional.
     * @param string $module Módulo a consultar.
     * @return JsonResponse Settings del módulo (200) o error (500).
     */
    public function getByModule(GetSettingByModuleRequest $request, string $module): JsonResponse
    {
        try {
            $onlyActive = $request->validated()['only_active'] ?? true;
            $settings = $this->settingService->getByModule($module, false, $onlyActive);
            return response()->json($settings, 200);
        } catch (\Exception $e) {
            Log::error("SettingController.getByModule: {$e->getMessage()}", ['module' => $module]);
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Actualizar setting específico.
     *
     * @param UpdateSettingRequest $request Datos validados con los settings a actualizar.
     * @param string $module Módulo al que pertenecen los settings.
     * @param string $key Clave del setting.
     * @return JsonResponse Settings actualizados (200) o error (500).
     */
    public function update(UpdateSettingRequest $request, string $module, string $key): JsonResponse
    {
        try {
            $validated = $request->validated();
            $group = $validated['group'] ?? '';
            $setting = $this->settingService->update($module, $key, $group, $validated['value'], $validated['is_active'] ?? null, false);

            if (!$setting) {
                return response()->json(['error' => 'Setting not found'], 404);
            }

            return response()->json($setting, 200);
        } catch (\Exception $e) {
            Log::error("SettingController.update: {$e->getMessage()}", ['module' => $module, 'key' => $key]);
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Actualizar uno o varios settings de un módulo.
     *
     * @param UpdateSettingByModuleRequest $request Datos validados con los settings a actualizar.
     * @param string $module Módulo al que pertenecen los settings.
     * @return JsonResponse Settings actualizados (200) o error (500).
     */
    public function updateByModule(UpdateSettingByModuleRequest $request, string $module): JsonResponse
    {
        try {
            $settings = $this->settingService->updateByModule($module, $request->validated());
            return response()->json($settings, 200);
        } catch (\Exception $e) {
            Log::error("SettingController.update: {$e->getMessage()}", ['module' => $module]);
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
