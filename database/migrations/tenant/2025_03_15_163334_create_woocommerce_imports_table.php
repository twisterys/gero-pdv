<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('woocommerce_imports', function (Blueprint $table) {
            $table->id();
            $table->string('type');
            $table->string('statut');
            $table->string('reference');
            $table->timestamp('last_imported_object')->nullable();
            $table->foreignId('magasin_id')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('woocommerce_imports');
    }
};
