<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGithubUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('github_users', function (Blueprint $table) {
            $table->integer('id')->primary();
            $table->string('login');
            $table->string('avatar_url')->nullable();
            $table->string('email')->nullable();
            $table->string('location')->nullable();
            $table->text('bio')->nullable();
            $table->dateTime('created_at')->nullable();
            $table->integer('followers')->default(0);
            $table->integer('following')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('github_users');
    }
}
