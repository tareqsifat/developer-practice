<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
public function up()
{
    Schema::create('blog_post_categories', function (Blueprint $table) {
        $table->foreignId('blog_post_id')->constrained();
        $table->foreignId('blog_category_id')->constrained();
        $table->primary(['blog_post_id', 'blog_category_id']);
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
