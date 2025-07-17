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
        Schema::create('references', function (Blueprint $table) {
            $table->charset('utf16');
            $table->collation('utf16_unicode_ci');
            $table->id();
            $table->string('nom')->nullable(false);
            $table->string('type')->nullable(false);
            $table->string('prefixe')->nullable(false);
            $table->string('format_date')->nullable();
            $table->unsignedInteger('longueur_compteur')->nullable(false);
            $table->string('separateur')->nullable();
            $table->string('emplacement_separateur')->nullable(false);
            $table->string('format_number')->nullable(false);
            $table->string('template')->nullable(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('references');
    }
};
