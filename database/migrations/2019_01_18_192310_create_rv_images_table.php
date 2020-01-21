<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRvImagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rv_images', function (Blueprint $table) {
            $table->increments('id');
            $table->string('url');
            $table->boolean('is_floorplan')->default(false);
            $table->integer('order')->nullable();
            $table->unsignedInteger('rv_id');
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
        Schema::dropIfExists('rv_images');
    }
}
