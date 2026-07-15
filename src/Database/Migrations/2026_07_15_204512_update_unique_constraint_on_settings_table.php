<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropUnique('uq_settings_module_key');
            $table->unique(['module', 'group', 'key'], 'uq_settings_module_group_key');
        });
    }

    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropUnique('uq_settings_module_group_key');
            $table->unique(['module', 'key'], 'uq_settings_module_key');
        });
    }
};
