<?php

namespace LechugaNegra\SettingManager\Models;

use Illuminate\Database\Eloquent\Model;

class SettingLog extends Model
{
    protected $table = 'settings_logs';

    public $timestamps = false;

    protected $fillable = [
        'data_module',
        'data_key',
        'data_type',
        'data_old_value',
        'data_new_value',
        'action',
        'user_id',
        'log_data',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'log_data' => 'array',
    ];
}
