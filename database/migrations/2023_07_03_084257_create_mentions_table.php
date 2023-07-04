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
        Schema::create('mentions', function (Blueprint $table) {
            $table->id();            
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('mentioned_user_id')->constrained('users')->onDelete('cascade')->onUpdate('cascade');

            $table->bigInteger('post_id')->nullable()->unsigned();
            $table->bigInteger('comment_id')->nullable()->unsigned();

            $table->foreign('post_id')->references('id')->on('posts')->onDelete('set null')->onUpdate('cascade');
            $table->foreign('comment_id')->references('id')->on('comments')->onDelete('set null')->onUpdate('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mentions');
    }
};
