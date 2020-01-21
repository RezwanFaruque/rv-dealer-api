<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RemoveBrochureColumnsFromModelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('rv_models', function (Blueprint $table) {
            $table->dropColumn(['brochure_url', 'brochure_title']);

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('rv_models', function (Blueprint $table) {

            $table->string('brochure_url')->nullable();
            $table->string('brochure_title')->nullable();
        });
    }
}
