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
        Schema::create('sections', function (Blueprint $table) {
            $table->id();

            $table->string('name', 25)->comment('Unique if type is 0');

            $table->unsignedTinyInteger('type')->comment('0, 1 or 2');

            $table->unsignedSmallInteger('position');
            
            $table->foreignId('section_id')->comment('null only if the section is type 0')->nullable()->constrained()->cascadeOnUpdate()->cascadeOnDelete();

            $table->foreignId('user_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sections');
    }
};
