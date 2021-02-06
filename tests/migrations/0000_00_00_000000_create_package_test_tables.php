<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePackageTestTables extends Migration
{
    public function up()
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
        });

        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->foreignId('role_id')->nullable();
            $table->string('email');

            $table->foreign('role_id')->references('id')->on('roles')->cascadeOnDelete();
        });

        Schema::create('posts', function (Blueprint $table) {
            $table->increments('id');
            $table->foreignId('user_id')->nullable();
            $table->string('post');

            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
        });
    }

    public function down()
    {
        Schema::dropIfExists('posts');
        Schema::dropIfExists('users');
    }
}
