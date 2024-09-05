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
        Schema::create('empresas',function(Blueprint $table){
            $table->id();
            $table->string('nombre');
            $table->string('direccion');
            $table->string('telefonos');
            $table->string('contacto');
            $table->string('pin');
            $table->string('puesto');
            $table->string('correo');
            $table->char('estado',1);
            $table->string('logo');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
        Schema::dropIfExists('empresas');
    }
};
