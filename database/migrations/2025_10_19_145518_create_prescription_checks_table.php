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
        Schema::create('prescription_checks', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id'); // Hekim
            $table->integer('molecule_id');
            $table->json('answers'); // Verilen cevaplar
            $table->json('lab_values'); // Girilen lab değerleri
            $table->boolean('is_eligible'); // Uygun mu?
            $table->text('result_message');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prescription_checks');
    }
};
