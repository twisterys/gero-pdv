<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // validé
        Schema::create('ventes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('client_id');
            $table->unsignedBigInteger('commercial_id')->nullable();
            $table->string('reference')->nullable();
            $table->string('statut')->required();
            $table->text('objet')->nullable();
            $table->date('date_document')->required();
            $table->date('date_expiration')->nullable();
            $table->date('date_emission')->nullable();
            $table->decimal('total_ht', 10, 2)->default(0)->required();
            $table->decimal('total_tva', 10, 2)->default(0)->required();
            $table->decimal('total_reduction', 10, 2)->default(0)->required();
            $table->decimal('total_ttc', 10, 2)->default(0)->required();
            $table->string('type_document')->required();
            $table->string('fichier_document')->nullable();
            $table->decimal('solde', 10, 2)->default(0);
            $table->decimal('encaisser', 10, 2)->default(0);
            $table->decimal('commission_par_defaut', 10, 2)->nullable()->default(0);
            // Foreign key constraint
            $table->foreign('created_by')->references('id')->on('users');
            $table->foreign('client_id')->references('id')->on('clients');
            $table->foreign('commercial_id')->nullable()->references('id')->on('commercials');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ventes');
    }
};
