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
        // Schema::create('molecule_rules', function (Blueprint $table) {
        //     $table->id();
        //     $table->integer('molecule_id');
        //     $table->string('rule_file_path'); // JSON dosya yolu: rules/molecule_1.json
        //     $table->integer('version')->default(1); // Kural versiyonu
        //     $table->boolean('is_active')->default(true);
        //     $table->text('notes')->nullable(); // Kurallarla ilgili notlar
        //     $table->timestamps();
        // });

        Schema::create('molecule_rules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('molecule_id')->constrained('molecules')->onDelete('cascade');
            $table->string('rule_file_path');
            $table->integer('version')->default(1);
            $table->boolean('is_active')->default(true);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('molecule_id');
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('molecule_rules');
    }
};
