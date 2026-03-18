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
    Schema::create('files', function (Blueprint $table) {

        $table->id();

        $table->string('file_name');

        $table->bigInteger('size');
        
        $table->enum('visibility', ['public', 'private'])
              ->default('private');
        $table->integer('downloads_count')
              ->default(0);
        $table->foreignId('folder_id')
              ->constrained()
              ->cascadeOnDelete();
        $table->foreignId('user_id')
              ->constrained()
              ->cascadeOnDelete();

        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('files');
    }
};
