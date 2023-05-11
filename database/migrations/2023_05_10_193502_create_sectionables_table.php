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
        Schema::create('sectionables', function (Blueprint $table) {
            $table->id();

            $table->morphs('sectionable');
            $table->foreignId('section_id')->constrained()->cascadeOnUpdate()->restrictOnDelete(); // It is necessary to delete the section's items after a section deletion and before delete a sectionable record
            $table->unsignedSmallInteger('position');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sectionables');
    }
};
