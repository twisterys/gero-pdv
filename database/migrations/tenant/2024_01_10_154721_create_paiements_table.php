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
        Schema::create('paiements', function (Blueprint $table) {
            $table->id();
            $table->integer('payable_id');
            $table->string('payable_type');
            $table->foreignId('client_id')->nullable()->references('id')->on('clients')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('compte_id')->references('id')->on('comptes')->cascadeOnDelete()->cascadeOnUpdate();
            $table->string('methode_paiement_key');
            $table->foreign('methode_paiement_key')->references('key')->on('methodes_paiement')->cascadeOnDelete()->cascadeOnUpdate();
            $table->boolean('comptable')->default(1);
            $table->date('date_paiement');
            $table->decimal('debit', 10, 2);
            $table->decimal('credit', 10, 2);
            $table->string('cheque_lcn_reference')->nullable();
            $table->date('cheque_lcn_date')->nullable();
            $table->string('note')->nullable();
            $table->string('recu')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('paiements');
    }
};
