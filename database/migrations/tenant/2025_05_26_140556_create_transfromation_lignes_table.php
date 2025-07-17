<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('transformation_lignes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('article_id')->nullable();
            $table->string('nom_article');
            $table->decimal('quantite');
            $table->string('type');
            $table->foreignId('transformation_id');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transformation_lignes');
    }
};
