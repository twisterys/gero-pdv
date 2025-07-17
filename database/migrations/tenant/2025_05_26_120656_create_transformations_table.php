<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('transformations', function (Blueprint $table) {
            $table->id();
            $table->string('reference');
            $table->dateTime('date');
            $table->string('status')->default('transformé');
            $table->string('object')->nullable();
            $table->string('note')->nullable();
            $table->foreignId('magasin_id')->nullable();
            $table->foreignId('created_by')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transformations');
    }
};
