<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // =============================================
        // 1. Lietotajs (Пользователь)
        // =============================================
        Schema::create('Lietotajs', function (Blueprint $table) {
            $table->integer('kodsID')->autoIncrement();
            $table->string('lietotajvards', 100)->unique();
            $table->string('epasts', 255)->unique();
            $table->string('parole', 255);
            $table->enum('statuss', ['aktivs', 'blokets'])->default('aktivs');
            $table->enum('loma', ['admins', 'registrets', 'viesis'])->default('viesis');
            $table->timestamps();
        });

        // =============================================
        // 2. Kategorijas (Категории)
        // =============================================
        Schema::create('Kategorijas', function (Blueprint $table) {
            $table->integer('ID')->autoIncrement();
            $table->string('nosaukums', 255);
            $table->string('slogan', 500);
            $table->timestamps();
        });

        // =============================================
        // 3. LietotajMajas (Связь пользователь-дом)
        // =============================================
        Schema::create('LietotajMajas', function (Blueprint $table) {
            $table->integer('LietotajMajasID')->autoIncrement();
            $table->integer('MajasID');
            $table->enum('statuss', ['pardots', 'var nopirkt'])->default('var nopirkt');
            $table->integer('LietotajsID');
            $table->timestamps();
            
           
            
            // Внешние ключи
            $table->foreign('LietotajsID')->references('kodsID')->on('Lietotajs')->onDelete('cascade');
        });

        // =============================================
        // 4. Majas (Дома/Недвижимость)
        // =============================================
        Schema::create('Majas', function (Blueprint $table) {
            $table->integer('MajasID')->autoIncrement();
            $table->string('pilseta', 255);
            $table->string('rajons', 100)->nullable();
            $table->string('adrese', 255);
            $table->decimal('cena', 12, 2);
            $table->decimal('platiba', 8, 2);
            $table->decimal('zemes_platiba', 8, 2)->nullable();
            $table->integer('istabu_skaits');
            $table->integer('stavu_skaits');
            $table->integer('celsanas_gads')->nullable();
            $table->string('stavoklis', 50)->nullable();
            $table->string('virsraksts', 255);
            $table->text('apraksts')->nullable();
            $table->string('ipasibas', 1000)->nullable();
            $table->integer('KategorijasID');
            $table->timestamps();
            
            // Внешние ключи
            $table->foreign('KategorijasID')->references('ID')->on('Kategorijas')->onDelete('restrict');
            
            // Индексы
            $table->index('KategorijasID');
        });

        // Добавляем второй внешний ключ для LietotajMajas (после создания Majas)
        Schema::table('LietotajMajas', function (Blueprint $table) {
            $table->foreign('MajasID')->references('MajasID')->on('Majas')->onDelete('cascade');
        });

        // =============================================
        // 5. Sludinajums (Объявление)
        // =============================================
        Schema::create('Sludinajums', function (Blueprint $table) {
            $table->integer('SludinajumsID')->autoIncrement();
            $table->string('attels', 500);
            $table->integer('LietotajsID')->nullable();;
            $table->integer('MajasID');
            $table->timestamps();
            
            // Внешние ключи
            $table->foreign('LietotajsID')->references('kodsID')->on('Lietotajs')->onDelete('cascade');
            $table->foreign('MajasID')->references('MajasID')->on('Majas')->onDelete('cascade');
            
            // Индексы
            $table->index('MajasID');
           
        });

        // =============================================
        // 6. Majas_atteli (Дополнительные фото дома)
        // =============================================
        Schema::create('Majas_atteli', function (Blueprint $table) {
            $table->integer('attels_id')->autoIncrement();
            $table->string('attels_fail', 255);
            $table->integer('attelu_kartiba')->default(0);
            $table->integer('MajasID');
            $table->timestamps();
            
            // Внешний ключ
            $table->foreign('MajasID')->references('MajasID')->on('Majas')->onDelete('cascade');
            
            // Индексы
            $table->index('MajasID');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('Majas_atteli');
        Schema::dropIfExists('Sludinajums');
        Schema::dropIfExists('Majas');
        Schema::dropIfExists('LietotajMajas');
        Schema::dropIfExists('Kategorijas');
        Schema::dropIfExists('Lietotajs');
    }
};