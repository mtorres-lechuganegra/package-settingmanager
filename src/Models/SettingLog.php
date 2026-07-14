<?php

namespace LechugaNegra\SettingManager\Models;

use Illuminate\Database\Eloquent\Model;

class SettingLog extends Model
{
    protected $table = 'settings_logs';

    public $timestamps = false;

    protected $fillable = [
        'data_id',
        'data_module',
        'data_code',
        'data_name',
        'data_type',
        'data_date',
        'data_status',
        'action',
        'user_id',
        'log_data',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'data_date' => 'datetime',
        'log_data' => 'array',
    ];
}
