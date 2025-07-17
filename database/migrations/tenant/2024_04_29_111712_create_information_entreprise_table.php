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
        Schema::create('information_entreprise', function (Blueprint $table) {
            $table->id();
            $table->enum('forme_juridique', ['sarl', 'sa', 'personne_physique', 'auto_entrepreneur']);
            $table->string('raison_social')->unique();
            $table->string('ice')->nullable();
            $table->string('email')->nullable();
            $table->string('telephone')->nullable();
            $table->text('note')->nullable();
            $table->text('adresse')->nullable();
            $table->string('RC')->nullable();
            $table->string('IF')->nullable();
            $table->string('ville')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('information_entreprise');
    }
};
