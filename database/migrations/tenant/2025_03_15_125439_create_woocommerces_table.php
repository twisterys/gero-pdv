<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('woocommerce_settings', function (Blueprint $table) {
            $table->id();
            $table->string('consumer_key')->nullable();
            $table->string('consumer_secret')->nullable();
            $table->string('store_url')->nullable();
            $table->string('price_value')->nullable();
            $table->string('version')->default('v3');
            $table->boolean('wp_api')->default(true);
            $table->boolean('verify_ssl')->default(false);
            $table->boolean('query_string_auth')->default(false);
            $table->integer('timeout')->default(15);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('woocommerce_settings');
    }
};
