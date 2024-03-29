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
        Schema::create('wysiwygs', function (Blueprint $table) {
            $table->id();

            $table->string('content', 14000);
            
            $table->unsignedSmallInteger('position');
            
            $table->foreignId('section_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();

            $table->foreignId('user_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wysiwygs');
    }
};
