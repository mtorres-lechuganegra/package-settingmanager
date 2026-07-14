<?php

namespace LechugaNegra\SettingManager\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use LechugaNegra\SettingManager\Services\SettingLogService;

class Setting extends Model
{
    protected $table = 'settings';

    protected $fillable = [
        'module',
        'group',
        'key',
        'type',
        'value',
        'description',
        'is_active',
        'is_locked',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_locked' => 'boolean',
    ];

    public function getValueAttribute(?string $raw): mixed
    {
        if ($raw === null) {
            return null;
        }

        return match ($this->type) {
            'integer' => (int) $raw,
            'float' => (float) $raw,
            'boolean' => filter_var($raw, FILTER_VALIDATE_BOOLEAN),
            'json',
            'array' => json_decode($raw, true),
            'encrypted' => Crypt::decryptString($raw),
            default => $raw,
        };
    }

    public function setValueAttribute(mixed $val): void
    {
        if ($this->is_locked) {
            throw new \RuntimeException("El setting [{$this->module}.{$this->key}] está bloqueado y no puede ser modificado por CRUD.");
        }

        $type = $this->attributes['type'] ?? $this->type ?? 'string';

        $this->attributes['value'] = match ($type) {
            'json',
            'array' => json_encode($val),
            'boolean' => $val ? 'true' : 'false',
            'encrypted' => Crypt::encryptString((string) $val),
            default => (string) $val,
        };
    }

    protected static function booted()
    {
        $events = ['created', 'updated', 'deleted'];

        foreach ($events as $event) {
            static::$event(function ($model) use ($event) {

                $safeFields = ['id', 'module', 'group', 'key', 'type', 'value', 'is_active', 'is_locked'];
                $logData = collect($model->toArray())->only($safeFields)->toArray();

                SettingLogService::register([
                    'data_id' => $model->id,
                    'data_module' => $model->module ?? null,
                    'data_code' => $model->group ? "{$model->group}.{$model->key}" : $model->key,
                    'data_name' => $model->value ?? null,
                    'data_type' => $model->type ?? null,
                    'data_date' => $model->created_at ?? null,
                    'data_status' => $model->is_active ? 'active' : 'inactive',
                    'action' => $event,
                    'user_id' => Auth::guard('api')->id(),
                    'log_data' => $logData,
                    'ip_address' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                ]);
            });
        }
    }
}
