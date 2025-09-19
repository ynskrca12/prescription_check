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
        Schema::create('molecule_lab_rules', function (Blueprint $table) {
            $table->id();
            $table->integer('molecule_id');
            $table->integer('laboratory_parameter_id');
            $table->enum('operator', ['>=', '<=', '=', '>', '<'])->nullable();
            $table->decimal('value', 8, 2);
            $table->string('explanation')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('molecule_lab_rules');
    }
};
