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
        Schema::create('affaires', function (Blueprint $table) {


            $table->unsignedBigInteger('client_id');
            $table->foreign('client_id')->references('id')->on('clients');
            $table->string('reference')->nullable();
            $table->string('titre')->nullable();
            $table->string('description')->nullable();
            $table->string('statut')->nullable();
            $table->decimal('budget_estimatif', 10, 2)->default(0)->nullable();
            $table->decimal('ca_global', 10, 2)->default(0)->nullable();
            $table->date('date_debut')->nullable();
            $table->date('date_fin')->nullable();
            $table->enum('cycle_type', ['jour', 'mois'])->default('mois')->nullable();
            $table->integer('cycle_duree')->default(0)->nullable();
            $table->id();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('affaires');
    }
};
