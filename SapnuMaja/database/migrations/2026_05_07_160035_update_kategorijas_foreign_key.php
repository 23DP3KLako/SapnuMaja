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
         // Сначала удаляем старый внешний ключ
        Schema::table('Majas', function (Blueprint $table) {
            $table->dropForeign(['KategorijasID']);
        });
        
        // Добавляем новый с CASCADE
        Schema::table('Majas', function (Blueprint $table) {
            $table->foreign('KategorijasID')
                  ->references('ID')
                  ->on('Kategorijas')
                  ->onDelete('cascade');  // Меняем с restrict на cascade
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('Majas', function (Blueprint $table) {
            $table->dropForeign(['KategorijasID']);
        });
        
        Schema::table('Majas', function (Blueprint $table) {
            $table->foreign('KategorijasID')
                  ->references('ID')
                  ->on('Kategorijas')
                  ->onDelete('restrict');
        });
    }
};
