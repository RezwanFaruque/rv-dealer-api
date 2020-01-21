<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRvModelsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('rv_models', function (Blueprint $table) {
            $table->unsignedInteger('id')->primary();
            $table->string('name');
            $table->unsignedInteger('brand_id');
            $table->string('brochure_url')->nullable();
            $table->string('brochure_title')->nullable();
            $table->unsignedInteger('count')->default(0);
            $table->unsignedInteger('count_new')->default(0);
            $table->timestamps();
            $table->foreign('brand_id')->references('id')->on('brands');
        });

        // pivot table between model and type
        Schema::create('model_type', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('model_id');
            $table->unsignedInteger('type_id');
            $table->unique(['model_id', 'type_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('model_type');
        Schema::dropIfExists('rv_models');
    }
}
