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
            $table->string('event_thumbnail',500)->default("https://blog.topcv.vn/wp-content/uploads/2019/02/nhan-vien-to-chuc-su-kien.jpg");
            $table->boolean('status')->default(1);
            $table->boolean('is_published')->default(0);
            $table->softDeletes();
            $table->string('user_id',36);
            $table->string('slug',500)->unique();
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
