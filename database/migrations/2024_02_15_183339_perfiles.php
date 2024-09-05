<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        //
        Schema::create('perfiles',function(Blueprint $table){
            $table->id();
            $table->string('perfilesdescrip');
            $table->tinyInteger('ingreso');
            $table->tinyInteger('egreso');
            $table->tinyInteger('requisiciones');
            $table->tinyInteger('calendarioentrevistas');
            $table->tinyInteger('usuariosdb');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
        Schema::dropIfExists('perfiles');
    }
};
