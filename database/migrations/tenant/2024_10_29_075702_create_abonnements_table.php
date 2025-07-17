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
        Schema::create('abonnements', function (Blueprint $table) {
            $table->id(); // ID
            $table->foreignId('client_id')->constrained('clients')->onDelete('cascade'); // client_id (FK)
            $table->foreignId('article_id')->constrained('articles')->onDelete('cascade'); // article_id (FK)
            $table->date('date_abonnement'); // date_abonnement
            $table->date('date_expiration'); // date_expiration
            $table->text('description')->nullable(); // Description
            $table->timestamps(); // timestamps
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('abonnements');
    }
};
