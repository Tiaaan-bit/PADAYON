<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('image_posts', function (Blueprint $table) {
            $table->id();

            $table->foreignId('author_id')->constrained('users')->cascadeOnDelete();

            $table->string('image_path');
            $table->timestamp('published_at')->nullable();
            $table->timestamps();

            $table->index(['published_at', 'id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('image_posts');
    }
};
