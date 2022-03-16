<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGithubUserRepositoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('github_user_repositories', function (Blueprint $table) {
            $table->integer('id')->primary();
            $table->integer('github_user_id');
            $table->index('github_user_id');
            $table->foreign('github_user_id')->references('id')->on('github_users');
            $table->string('name');
            $table->string('full_name');
            $table->integer('forks_count')->default(0);
            $table->integer('stargazers_count')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('github_user_repositories');
    }
}
