<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('options', function (Blueprint $table) {
            $table->string('slug')->primary();
            $table->timestamps();
        });

        Schema::create('option_rv', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('rv_id');
            $table->string('option_slug');
            $table->unique(['rv_id', 'option_slug']);
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
        Schema::dropIfExists('options');
    }
}
