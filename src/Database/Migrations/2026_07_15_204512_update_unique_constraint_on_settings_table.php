<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $indexes = collect(DB::select("SHOW INDEX FROM settings WHERE Non_unique = 0"))
                ->groupBy('Key_name')
                ->filter(function ($columns) {
                    $cols = $columns->pluck('Column_name')->sort()->values()->toArray();
                    return $cols === ['key', 'module'];
                })
                ->keys();

            foreach ($indexes as $index) {
                $table->dropUnique($index);
            }

            $table->unique(['module', 'group', 'key'], 'uq_settings_module_group_key');
        });
    }

    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $indexes = collect(DB::select("SHOW INDEX FROM settings WHERE Non_unique = 0"))
                ->groupBy('Key_name')
                ->filter(function ($columns) {
                    $cols = $columns->pluck('Column_name')->sort()->values()->toArray();
                    return $cols === ['group', 'key', 'module'];
                })
                ->keys();

            foreach ($indexes as $index) {
                $table->dropUnique($index);
            }

            $table->unique(['module', 'key'], 'uq_settings_module_key');
        });
    }
};
