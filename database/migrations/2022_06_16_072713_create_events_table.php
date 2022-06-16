<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEventsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('events', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('title',255);
            $table->bigInteger('category_id')->unsigned();
            $table->text('content')->collation('utf8mb4_vietnamese_ci')->nullable();
            $table->date('deadline');
            $table->boolean('status')->default(1);
            $table->boolean('is_published')->default(0);
            $table->boolean('is_deleted')->default(0);
            $table->string('user_id',36);
            $table->timestamps();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('category_id')->references('id')->on('category');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('events');
    }
}
