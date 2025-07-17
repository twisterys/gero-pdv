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
        Schema::create('transfert_caisse', function (Blueprint $table) {
            $table->id();
            $table->foreignId('compte_source_id')->constrained('comptes');
            $table->foreignId('compte_destination_id')->constrained('comptes');
            $table->date('date_emission');
            $table->date('date_reception');
            $table->decimal('montant', 10, 2); // 10 digits, 2 decimals
            $table->string('description', 255)->nullable();
            $table->string('methode_paiement_key');
            $table->foreign('methode_paiement_key')->references('key')->on('methodes_paiement')->cascadeOnDelete()->cascadeOnUpdate();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transfert_caisse');
    }
};
