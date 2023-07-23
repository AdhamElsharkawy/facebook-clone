<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('name');
            $table->string('email')->unique();
            $table->string('image')->default('assets/images/user.png');
            $table->string('mobile')->nullable();
            $table->string('role')->default('user');
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password')->nullable();
            $table->string('status')->default('active'); // or blocked
            $table->date('birth_date')->nullable();
            $table->integer('global_score')->default(1);
            $table->integer('team_score')->default(1);
            $table->json("social_links")->nullable();
            $table->string('front_theme')->default('light');
            $table->string('back_theme')->default('light');
            $table->bigInteger('department_id')->unsigned()->nullable();

            $table->rememberToken();
            $table->timestamps();

            $table->foreign('department_id')->references('id')->on('departments')->onDelete('set null')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
