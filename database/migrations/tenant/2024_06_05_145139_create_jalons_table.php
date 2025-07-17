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
        Schema::create('jalons', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('affaire_id');
            $table->foreign('affaire_id')->references('id')->on('affaires')->onDelete('cascade');

            $table->string('nom');
            $table->date('date');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jalons');
    }
};
