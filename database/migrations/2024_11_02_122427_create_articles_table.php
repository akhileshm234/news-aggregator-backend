<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateArticlesTable extends Migration
{
    public function up()
    {
        Schema::create('articles', function (Blueprint $table) {
            $table->id();
            $table->string('source_id');
            $table->string('source');
            $table->string('title');
            $table->text('content')->nullable();
            $table->string('url');
            $table->timestamp('published_at');
            $table->string('author')->nullable();
            $table->string('image_url')->nullable();
            $table->string('content_hash')->index(); // Add this line
            $table->timestamps();
    
            // Composite indexes
            $table->unique(['source_id', 'source']);
            $table->index('published_at');
            $table->index('title');
        });
    }

    public function down()
    {
        Schema::dropIfExists('articles');
    }
} 
