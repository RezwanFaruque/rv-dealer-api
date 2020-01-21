<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRvsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rvs', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title');
            $table->unsignedInteger('type_id');
            $table->unsignedInteger('brand_id');
            $table->unsignedInteger('model_id')->nullable();
            $table->boolean('condition');
            $table->string('stock_number');
            $table->string('year');
            $table->string('floorplan')->nullable();
            $table->integer('length')->nullable();
            $table->integer('length_inches')->nullable();
            $table->integer('unit')->nullable(); //(Length * 12) + Length_Inches
            $table->decimal('msrp',12,2)->nullable();
            $table->decimal('price',12,2)->nullable();
            $table->decimal('price_field',12,2);
            $table->decimal('monthly_payment',10,2);
            $table->decimal('saving',12,2)->nullable();
            $table->decimal('discount',12,2)->nullable();
            $table->decimal('sale_saving',12,2)->nullable();
            $table->decimal('sale_discount',12,2)->nullable();
            $table->decimal('sale_price',10,2)->nullable();
            $table->decimal('freight_cost',10,2)->nullable();
            $table->text('floorplan_image')->nullable();
            $table->string('fp_id')->nullable();


            $table->text('description')->nullable();
            $table->text('description_dealer_only')->nullable();
            $table->string('chassis')->nullable();
            $table->string('engine_manufacturer')->nullable();
            $table->string('engine_model')->nullable();
            $table->string('fuel_type')->nullable();
            $table->string('interior_color')->nullable();
            $table->string('exterior_color')->nullable();

            $table->string('sleep_capacity')->nullable();
            $table->string('water_capacity_fresh')->nullable();
            $table->string('water_capacity_black')->nullable();
            $table->string('water_capacity_grey')->nullable();
            $table->integer('num_air_conditioners')->nullable();
            $table->string('ac_spec')->nullable();
            $table->integer('mileage')->nullable();
            $table->string('gross_vehicle_weight')->nullable();
            $table->string('dry_weight')->nullable();
            $table->string('payload_capacity')->nullable();
            $table->string('hitch_weight')->nullable();
            $table->string('trailer_weight')->nullable();
            $table->integer('num_slideouts')->nullable();
            $table->integer('num_axles')->nullable();
            $table->integer('num_side_doors')->nullable();
            $table->string('rear_opening')->nullable();
            $table->string('hitch_type')->nullable();
            $table->string('capacity')->nullable();
            $table->string('width')->nullable();
            $table->string('height')->nullable();
            $table->string('tongue_weight')->nullable();
            $table->string('warranty')->nullable();
            $table->string('short_wall_length')->nullable();
            $table->string('construction')->nullable();
            $table->string('load_type')->nullable();
            $table->string('floor_type')->nullable();
            $table->string('conversion_type')->nullable();
            $table->string('num_horses')->nullable();
            $table->string('vin')->nullable(); // ask if it's string
            $table->string('headline')->nullable();
            $table->string('youtube_link')->nullable();
            $table->string('youtube_link_2')->nullable();
            $table->string('youtube_link_3')->nullable();
            $table->string('youtube_link_4')->nullable();
            $table->string('tour_360_url')->nullable();
            $table->string('customer_review_link')->nullable();


            $table->boolean('is_special');
            $table->boolean('is_sold');
            $table->boolean('is_consignment');
            $table->boolean('is_new_arrival');
            $table->boolean('is_on_deposit');
            $table->boolean('is_on_order');
            $table->boolean('is_reduced');
            $table->boolean('natda_cpo')->nullable();
            $table->string('nada_url')->nullable();
            $table->boolean('use_special_pricing');
            $table->boolean('is_active');
            $table->boolean('use_click_to_call');
            $table->boolean('use_get_low_price');
            $table->boolean('hide_on_dealer_site');
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
        Schema::dropIfExists('rvs');
    }
}
