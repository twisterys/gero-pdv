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
        Schema::create('releve_bancaires', function (Blueprint $table) {
            $table->id();
            $table->foreignId("compte_id")->nullable()->references('id')->on('comptes')->cascadeOnUpdate()->nullOnDelete();
            $table->string('url')->nullable();
            $table->integer('year');
            $table->integer('month');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('releve_bancaires');
    }
};
