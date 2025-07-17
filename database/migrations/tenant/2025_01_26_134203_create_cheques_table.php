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
        Schema::create('cheques', function (Blueprint $table) {
            $table->id();
            $table->string("type");
            $table->string("number")->nullable();
            $table->decimal("montant", 10, 2);
            $table->date("date_emission");
            $table->date('date_echeance');
            $table->string('statut')->default('en_attente');
            $table->foreignId('banque_id')->nullable()->references('id')->on('banques')->cascadeOnUpdate()->nullOnDelete();
            $table->foreignId('client_id')->nullable()->references('id')->on('clients')->cascadeOnUpdate()->nullOnDelete();
            $table->foreignId("compte_id")->nullable()->references('id')->on('comptes')->cascadeOnUpdate()->nullOnDelete();
            $table->foreignId("fournisseur_id")->nullable()->references('id')->on('fournisseurs')->cascadeOnUpdate()->nullOnDelete();
            $table->text("note")->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cheques');
    }
};
