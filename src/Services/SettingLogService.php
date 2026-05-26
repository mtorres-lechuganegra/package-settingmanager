<?php

namespace LechugaNegra\SettingManager\Services;

use Illuminate\Support\Facades\Log;
use LechugaNegra\SettingManager\Models\SettingLog;

class SettingLogService
{
    /**
     * Registrar un log de auditoría para un setting.
     *
     * @param string $table Tabla de logs donde se registrará la entrada.
     * @param array $data Datos del log a registrar.
     * @return void
     */
    public static function register(string $table, array $data): void
    {
        try {
            SettingLog::create($data);
        } catch (\Exception $e) {
            Log::error("SettingLogService.register: {$e->getMessage()}", $data);
        }
    }
}
