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
        Schema::create('articles', function (Blueprint $table) {
            $table->id();
            $table->string('designation');
            $table->string('reference');
            $table->text('description')->nullable();
            $table->float('prix_achat',11,2)->nullable();
            $table->float('prix_revient',11,2)->nullable();
            $table->float('prix_vente',11,2)->nullable();
            $table->string('prix_mode');
            $table->enum('stockable',[0,1])->default(1);
            // $table->integer('quantite_alerte')->default(0);
            $table->string('image')->nullable();
            $table->foreignId('unite_id')->references('id')->on('unites')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('famille_id')->nullable()->references('id')->on('familles')->cascadeOnUpdate()->cascadeOnDelete();
            $table->float('taxe',11,2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('articles');
    }
};
