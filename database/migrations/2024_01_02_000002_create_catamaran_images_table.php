<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('catamaran_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('catamaran_id')->constrained()->cascadeOnDelete();
            $table->string('image_path', 500);
            $table->string('image_alt')->nullable();
            $table->boolean('is_primary')->default(false);
            $table->integer('sort_order')->default(0);
            $table->timestamp('created_at')->useCurrent();

            $table->index('catamaran_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('catamaran_images');
    }
};
