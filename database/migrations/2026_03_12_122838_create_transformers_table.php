<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::create('transformers', function (Blueprint $table) {
            $table->id();
            $table->string('name'); 
            $table->string('address')->nullable();
            $table->integer('capacity')->default(500);
            $table->float('current_load')->default(0);
            $table->float('temperature')->default(20);
            $table->string('status')->default('normal');
            $table->timestamps();
        });
    }   


    public function down(): void
    {
        Schema::dropIfExists('transformers');
    }
};
