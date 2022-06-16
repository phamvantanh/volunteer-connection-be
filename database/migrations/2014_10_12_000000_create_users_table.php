<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

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
            $table->string('id',36)->unique();
            $table->string('name',255);
            $table->string('email',255)->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password',64);
            $table->enum('role',['volunteer','organization','admin']);
            $table->string('avatar_url',500)->nullable();
            $table->enum('gender',['male','female','unknown'])->default('unknown');
            $table->string('phone',20);
            $table->date('date_of_birth');
            $table->text('about')->nullable();;
            $table->boolean('is_disable')->default(0);
            $table->timestamps();
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
