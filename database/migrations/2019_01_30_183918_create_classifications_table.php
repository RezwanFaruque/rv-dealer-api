<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateClassificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('classifications', function (Blueprint $table) {
            $table->string('slug')->primary();
            $table->timestamps();
        });


        Schema::create('classification_rv', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('rv_id');
            $table->string('classification_slug');
            $table->unique(['rv_id', 'classification_slug']);
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
        Schema::dropIfExists('classifications');
    }
}
