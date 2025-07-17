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
        Schema::create('abonnement_settings', function (Blueprint $table) {
            $table->id();
            $table->text('emails')->nullable();
            $table->text('content')->nullable();
            $table->string('subject')->nullable();
            $table->boolean('notifier_client')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('abonnement_settings');
    }
};
