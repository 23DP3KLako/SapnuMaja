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
       
        Schema::create('Lietotajs', function (Blueprint $table) {
            $table->integer('kodsID')->autoIncrement();
            $table->string('lietotajvards', 100)->unique();   
            $table->string('epasts', 255)->unique();          
            $table->string('parole', 255);                    
            $table->enum('loma', ['admins', 'registrets', 'viesis'])->default('registrets');
            $table->enum('statuss', ['aktivs', 'blokets'])->default('aktivs');
            $table->date('registresanas_datums');
            $table->timestamps();
        });

        Schema::create('Majas_plans', function (Blueprint $table) {
            $table->integer('Majas_plans_ID')->autoIncrement();
            $table->string('nosaukums', 255);
            $table->string('izskats', 255)->nullable();
            $table->string('apraksts', 1000)->nullable();
            $table->timestamps();
        });

        Schema::create('Majas', function (Blueprint $table) {
            $table->integer('Majas_ID')->autoIncrement();
            $table->string('adrese', 255);
            $table->string('pilseta', 100);
            $table->decimal('cena', 12, 2);
            $table->decimal('platiba', 8, 2)->nullable();
            $table->integer('istabu_skaits')->nullable();
            $table->string('ipasibas', 1000)->nullable();
            $table->integer('Majas_plans_ID');
            $table->timestamps();
        });

        Schema::table('Majas', function (Blueprint $table) {
            $table->foreign('Majas_plans_ID')
                  ->references('Majas_plans_ID')->on('Majas_plans');
        });

        Schema::create('Sludinajums', function (Blueprint $table) {
            $table->integer('Sludinajums_ID')->autoIncrement();
            $table->enum('statuss', ['aktivs', 'arhivets'])->default('aktivs');
            $table->date('publikacijas_datums');
            $table->date('beigu_datums')->nullable();
            $table->integer('skaits')->default(0);
            $table->integer('Majas_ID');
            $table->integer('Izveidotajs_ID'); 
            $table->timestamps();
        });

        Schema::table('Sludinajums', function (Blueprint $table) {
            $table->foreign('Majas_ID')
                  ->references('Majas_ID')->on('Majas')->onDelete('cascade');
            $table->foreign('Izveidotajs_ID')
                  ->references('kodsID')->on('Lietotajs');
        });


        Schema::create('Izlase', function (Blueprint $table) {
            $table->integer('Izlase_ID')->autoIncrement();
            $table->date('pievienosanas_datums');
            $table->string('piezimes', 500)->nullable();
            $table->integer('Lietotajs_ID');
            $table->integer('Sludinajums_ID');
            $table->timestamps();

            $table->unique(['Lietotajs_ID', 'Sludinajums_ID']);
        });

        Schema::table('Izlase', function (Blueprint $table) {
            $table->foreign('Lietotajs_ID')
                  ->references('kodsID')->on('Lietotajs')->onDelete('cascade');
            $table->foreign('Sludinajums_ID')
                  ->references('Sludinajums_ID')->on('Sludinajums')->onDelete('cascade');
        });
    


    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('Izlase');
        Schema::dropIfExists('Sludinajums');
        Schema::dropIfExists('Majas');
        Schema::dropIfExists('Majas_plans');
        Schema::dropIfExists('Lietotajs');
    }
};
