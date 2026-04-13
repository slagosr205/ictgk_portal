<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('perfiles', function (Blueprint $table) {
            if (! Schema::hasColumn('perfiles', 'seguridad')) {
                $table->boolean('seguridad')->default(false)->after('visualizarinformes');
            }
        });

        // Ensure a dedicated "seguridad" profile exists.
        $exists = DB::table('perfiles')->where('perfilesdescrip', 'seguridad')->exists();
        if (! $exists) {
            DB::table('perfiles')->insert([
                'perfilesdescrip' => 'seguridad',
                'ingreso' => 0,
                'egreso' => 0,
                'bloqueocolaborador' => 0,
                'gestiontablas' => 0,
                'visualizarinformes' => 0,
                'seguridad' => 1,
            ]);
        }
    }

    public function down(): void
    {
        Schema::table('perfiles', function (Blueprint $table) {
            if (Schema::hasColumn('perfiles', 'seguridad')) {
                $table->dropColumn('seguridad');
            }
        });

        // Do not delete rows on down.
    }
};
