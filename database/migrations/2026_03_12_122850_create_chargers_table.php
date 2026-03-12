<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
 
    public function up(): void
    {
        Schema::create('chargers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transformer_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->integer('max_power')->default(22);
            $table->string('status')->default('free');
            $table->timestamps();
        });
    }


    public function down(): void
    {
        Schema::dropIfExists('chargers');
    }
};
