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
        Schema::create('categorias', function (Blueprint $table) {
            $table->id();
            $table->string('nome');
            $table->timestamps();
        });

        Schema::table('contas_pagars', function (Blueprint $table) {
            $table->foreignId('categoria_id')->nullable();
        });
        Schema::table('contas_recebers', function (Blueprint $table) {
            $table->foreignId('categoria_id')->nullable();
        });

        Schema::table('custo_veiculos', function (Blueprint $table) {
            $table->foreignId('categoria_id')->nullable();

            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categorias');

        Schema::table('contas_pagars', function (Blueprint $table) {
          
            $table->dropColumn('categoria_id');
        });

        Schema::table('contas_pagars', function (Blueprint $table) {
          
            $table->dropColumn('categoria_id');
        });

        Schema::table('custo_veiculos', function (Blueprint $table) {
          
            $table->dropColumn('categoria_id');
        });
    }
};
