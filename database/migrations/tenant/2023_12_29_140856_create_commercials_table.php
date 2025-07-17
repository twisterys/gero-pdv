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
        Schema::create('commercials', function (Blueprint $table) {
            $table->id();
            // $table->enum('forme_juridique', ['sarl', 'sa', 'personne_physique', 'auto_entrepreneur', 'sos', 'gie', 'snc', 'scs', 'sca']);
            $table->string('reference', 20);
            $table->string('nom');
            $table->string('ice')->nullable();
            $table->string('email')->nullable();
            $table->string('telephone')->nullable();
            $table->text('note')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('commercials');
    }
};
