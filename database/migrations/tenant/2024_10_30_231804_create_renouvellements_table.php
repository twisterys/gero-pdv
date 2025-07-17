<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('renouvellements', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('abonnement_id');
            $table->date('date_renouvellement');
            $table->date('date_expiration');
            $table->decimal('montant', 8, 2);
            $table->string('note')->nullable();
            $table->string('document_reference')->nullable(); // Nouveau champ document_reference
            $table->timestamps();

            $table->foreign('abonnement_id')->references('id')->on('abonnements')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('renouvellements');
    }
};
