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
        Schema::create('comptes', function (Blueprint $table) {
            $table->charset('utf16');
            $table->collation('utf16_unicode_ci');
            $table->id();
            $table->string('nom');
            $table->string('type');
            $table->boolean('statut')->default(0);
            $table->string('banque')->nullable();
            $table->string('rib')->nullable();
            $table->string('adresse')->nullable();
            $table->boolean('principal')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('comptes');
    }
};
