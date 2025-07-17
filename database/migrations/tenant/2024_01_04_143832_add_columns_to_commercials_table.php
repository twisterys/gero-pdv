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
        Schema::table('commercials', function (Blueprint $table) {
            $table->decimal('commission_par_defaut', 10, 2)->nullable()->comment('commission par défaut de commercial');
            $table->decimal('objectif', 10, 2)->nullable()->comment('Objectif de commercial');
            // Add a new column for zone geographique
            $table->text('secteur')->nullable()->comment('secteur de commercial');
            $table->text('type_commercial')->nullable()->comment('Type de commercial');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('commercials', function (Blueprint $table) {
            $table->dropColumn(['commerciaux_par_defaut', 'objectif', 'zone_geographique', 'type_commercial']);
        });
    }
};
