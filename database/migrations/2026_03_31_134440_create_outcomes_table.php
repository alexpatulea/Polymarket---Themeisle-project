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
        Schema::create('outcomes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('market_id')->constrained()->onDelete('cascade');
            $table->string('name'); // Ex: "Da", "Nu", "Candidatul A"
            $table->unsignedBigInteger('pool')->default(0); // Banii pariați pe această opțiune
            $table->boolean('is_winner')->nullable(); // Null = nerezolvat, True = a câștigat, False = a pierdut
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('outcomes');
    }
};
