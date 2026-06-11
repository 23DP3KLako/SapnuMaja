<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('LietotajMajas', function (Blueprint $table) {
            // Добавляем столбец SludinajumsID
            $table->integer('SludinajumsID')->nullable()->after('LietotajsID');
            
            // Добавляем внешний ключ
            $table->foreign('SludinajumsID')
                  ->references('SludinajumsID')
                  ->on('Sludinajums')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('LietotajMajas', function (Blueprint $table) {
            $table->dropForeign(['SludinajumsID']);
            $table->dropColumn('SludinajumsID');
        });
    }
};