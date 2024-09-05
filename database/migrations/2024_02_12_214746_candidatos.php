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
        Schema::create('candidatos',function(Blueprint $table){
            $table->id();
            $table->integer('identidad')->unique();
            $table->string('nombre');
            $table->string('apellido');
            $table->string('telefono');
            $table->string('correo');
            $table->string('direccion');
            $table->char('generoM_F',1);
            $table->date('fecha_nacimiento');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
        Schema::dropIfExists('candidatos');
    }
};
