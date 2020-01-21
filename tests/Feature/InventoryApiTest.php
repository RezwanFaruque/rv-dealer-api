<?php

namespace Tests\Feature;

use App\Brand;
use App\Classification;
use App\Rv;
use App\RvModel;
use App\Type;
use App\User;
use GuzzleHttp\Client;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;

class InventoryApiTest extends TestCase
{
    use DatabaseMigrations, WithFaker;

    const API_NAMESPACE = 'v1';


    /**
     * @test
     */

    public function test_get_models_of_type_that_have_inventory()
    {
        $query_classification_slug_1 ="Class_A";
        $query_classification_slug_2 = "Class_B";

        factory(Classification::class)->create([
            'slug' => $query_classification_slug_1
        ]);
        $array_models_belongs_to_classification_1 = [ 555, 666, 332, 111, 130];
        $array_models_belongs_to_classification_2 = [ 555, 888, 212, 33, 12]; // only model id in common
        $array_models_belongs_to_classification_old = [ 99, 89, 88, 86, 58]; // with condition old



        foreach ($array_models_belongs_to_classification_1 as $item)
        {
            $brand = factory(Brand::class)->create();
            $model = RvModel::find($item);
            if(! $model ){
                factory(RvModel::class)->create([
                    'id' => $item,
                    'brand_id' => $brand->id,
                ]);
            }

            $rv = factory(Rv::class)->create([
                'model_id' => $item,
                'is_sold' => 0,
                'brand_id' => $brand->id,
                'condition' => 1

            ]);
            $rv->classifications()->attach($query_classification_slug_1);
        }
        foreach ($array_models_belongs_to_classification_2 as $item)
        {
            $brand = factory(Brand::class)->create();
            $model = RvModel::find($item);
            if(! $model ){
                factory(RvModel::class)->create([
                    'id' => $item,
                    'brand_id' => $brand->id,
                ]);
            }

            $rv = factory(Rv::class)->create([
                'model_id' => $item,
                'is_sold' => 0,
                'brand_id' => $brand->id,
                'condition' => 1

            ]);
            $rv->classifications()->attach($query_classification_slug_2);
        }

        foreach ($array_models_belongs_to_classification_old as $item)
        {
            $brand = factory(Brand::class)->create();
            factory(RvModel::class)->create([
                'id' => $item,
                'brand_id' => $brand->id,
            ]);
            $rv = factory(Rv::class)->create([
                'model_id' => $item,
                'brand_id' => $brand->id,
                'is_sold' => 0,
                'condition' => 0
            ]);
            $rv->classifications()->attach($query_classification_slug_1);
        }

        factory(Rv::class , 3)->create([
            'model_id' => 2222,
            'is_sold' => 0,
        ]);



        $uri = '/inventory/classifications/' . $query_classification_slug_1 . '/models?filter[condition]=1';
        $response = $this->connect($uri );
        $this->assertEquals(
            count($array_models_belongs_to_classification_1),
            count($response->json('data'))
        );

        $uri = '/inventory/classifications/' . $query_classification_slug_2 . '/models?filter[condition]=1';
        $response = $this->connect($uri );
        $this->assertEquals(
            count($array_models_belongs_to_classification_2),
            count($response->json('data'))
        );
        $uri = '/inventory/classifications/' . $query_classification_slug_1 . '|'.$query_classification_slug_2 . '/models?filter[condition]=1';
        $response = $this->connect($uri );
        $this->assertEquals(
            count($array_models_belongs_to_classification_1) + count($array_models_belongs_to_classification_2),
            count($response->json('data'))
        );
        $uri = '/inventory/classifications/' . $query_classification_slug_1 . '/models?filter[condition]=0';
        $response = $this->connect($uri );
        $this->assertEquals(
            count($array_models_belongs_to_classification_old) ,
            count($response->json('data'))
        );
    }

    /**
     * @test
     */
    /*public function test_get_models_of_type_that_have_inventory()
    {
        $query_type_id =2;

        $array_models_belongs_to_type = [ 555, 666, 332, 111, 130];
        foreach ($array_models_belongs_to_type as $item)
        {
            factory(RvModel::class)->create([
               'id' => $item
            ]);
            factory(Rv::class)->create([
                'type_id' => $query_type_id,
                'model_id' => $item,
                'is_sold' => 0,
            ]);
            factory(Rv::class)->create([
                'type_id' => $query_type_id,
                'model_id' => $item,
                'is_sold' => 1,
            ]);
        }
        factory(Rv::class , 3)->create([
            'type_id' => 1,
            'model_id' => 222,
            'is_sold' => 0,
        ]);



        $uri = '/inventory/types/' . $query_type_id . '/models';
        $response = $this->connect($uri );
        $this->assertEquals(
            count($array_models_belongs_to_type),
            count($response->json('data'))
        );
    }*/

    /**
     * @test
     */
    public function test_get_models_of_brand_that_have_inventory()
    {
        $number_model_with_count = 8;
        $brand = factory(Brand::class, 1)->create([]);
        factory(RvModel::class, $number_model_with_count)->create([
            'count' => 2,
            'brand_id' => $brand[0]->id
        ]);


        $uri = '/inventory/brands/' . $brand[0]->id . '/models';
        $response = $this->connect($uri );
        $this->assertEquals(
            $number_model_with_count,
            count($response->json('data'))
        );
    }



    /**
     * @test
     */
    public function test_get_models_that_have_inventory()
    {
        $number_model_with_count = 5;
        $brand = factory(Brand::class, 1)->create([]);
        factory(RvModel::class, $number_model_with_count)->create([
            'count' => 2,
            'brand_id' => $brand[0]->id
        ]);


        $uri = '/inventory/models';
        $response = $this->connect($uri );
        $this->assertEquals(
            $number_model_with_count,
            count($response->json())
        );
    }


    /**
     * @test
     */
    public function test_get_brands_that_have_inventory()
    {
        factory(Brand::class, 1)->create([
            'id' => '200',
            'count' => 1
        ]);
        factory(Brand::class, 1)->create([
            'id' => '113',
            'count' => 0

        ]);
        factory(Rv::class, 1)->create([
            'brand_id' => '200',
        ]);

        $uri = '/inventory/brands';
        $response = $this->connect($uri );
        $this->assertEquals(
            1,
            count($response->json())
        );
    }

    protected function connect($uri, $params = [])
    {
        // authenticate a user
        $user = factory(User::class)->create();
        $this->actingAs($user, 'api');

        // call the api.
        $url = '/api/' . self::API_NAMESPACE .  $uri;
        return $this->call('GET',  $url , $params);
    }

}
