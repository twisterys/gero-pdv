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
        Schema::create('depenses', function (Blueprint $table) {
            $table->id();
            $table->string('reference');
            $table->string('nom_depense');
            $table->foreignId('categorie_depense_id')->nullable()->references('id')->on('categorie_depense')->cascadeOnDelete()->cascadeOnUpdate();;
            $table->string('pour');
            $table->float('montant_ht', 10, 2)->default(0);
            $table->float('montant_ttc', 10, 2)->default(0);
            $table->float('tva', 10, 2)->default(0);
            $table->date('date_operation');
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('depenses');
    }
};
