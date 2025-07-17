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
        Schema::create('achats', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->nullable();
            $table->string('reference_externe');
            $table->foreignId('fournisseur_id')->references('id')->on('fournisseurs')->cascadeOnUpdate()->cascadeOnDelete();
            $table->string('objet')->nullable();
            $table->string('statut')->nullable();
            $table->string('type_document')->nullable();
            $table->date('date_expiration')->nullable();
            $table->date('date_emission')->nullable();
            $table->string('fichier_document')->nullable();
            $table->text('note')->nullable();
            $table->string('statut_paiement')->nullable();
            $table->text('piece_jointe')->nullable();
            $table->decimal('total_ht',10,2)->nullable();
            $table->decimal('total_tva',10,2)->nullable();
            $table->decimal('total_reduction',10,2)->nullable();
            $table->decimal('total_ttc',10,2)->nullable();
            $table->decimal('debit',10,2)->nullable();
            $table->decimal('credit',10,2)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('achats');
    }
};
