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
        Schema::create('pets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('pet_type_id')->constrained('pet_types')->onDelete('cascade');
            $table->string('name');
            $table->string('breed')->nullable();
            $table->enum('gender', ['male', 'female'])->default('male');
            $table->date('birthdate')->nullable();
            $table->boolean('is_fully_vaccinated')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pets');
    }
};
