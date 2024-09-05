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
        Schema::create('egresos_ingresos',function(Blueprint $table){
            $table->id();
            $table->integer('identidad');
            $table->integer('id_empresa');
            $table->date('fechaIngreso');
            $table->string('area');
            $table->integer('id_puesto');
            $table->char('activo',1);
            $table->string('forma_egreso');
            $table->string('Comentario');
            $table->char('recomendado',1);
            $table->char('recontrataria',1);
            $table->char('prohibirIngreso',1);
            $table->string('ComenProhibir');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
        Schema::dropIfExists('egresos_ingresos');
    }
};
