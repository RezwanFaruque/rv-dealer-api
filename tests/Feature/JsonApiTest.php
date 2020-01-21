<?php

namespace Tests\Feature;

use App\Rv;
use App\User;
use GuzzleHttp\Client;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class JsonApiTest extends TestCase
{
    use DatabaseMigrations, WithFaker;

    const API_NAMESPACE = 'v1';


    /**
     * @test
     */
    public function user_can_filter_with_combined_class_keyword()
    {

        $title = "2005 Winnebago Adventurer 35U Class A Gas RV for Sale at MHSRV";
        $stock_number = 'MG001SXZ11';
        factory(Rv::class)->create([
            'stock_number' => $stock_number,
            'title' => $title,
            'condition' => 1,
            'year' => 2009
        ]);

        factory(Rv::class)->create([
            'stock_number' => 'MG001SXZ12',
            'title' => "2005 Winnebago Adventurer 35U Class B Gas RV for Sale at MHSRV",
            'condition' => 0,
            'year' => 2009
        ]);
        

        $uri = "/rvs";


        $keyword= "class a";
        $param_str = "filter[keyword]=$keyword&fields[rvs]=stock_number";

        $params = [];
        parse_str($param_str , $params);
        $response = $this->connect($uri , $params);
        $this->assertEquals(
            1,
            $response->json("meta.page.total")
        );

        $this->assertEquals(
            'MG001SXZ11',
            $response->json("data")[0]["attributes"]["stock_number"]
        );


    }
    /**
     * @test
     */
    public function user_can_filter_with_keyword_with_any_words_order()
    {

        $title = "New 2009 Sportscoach Legend RV  (500 HP-TG) w/4 Slides";
        $stock_number = 'MG001SXZ11';
        factory(Rv::class)->create([
            'stock_number' => $stock_number,
            'title' => $title,
            'condition' => 1,
            'year' => 2009
        ]);

        factory(Rv::class)->create([
            'stock_number' => 'MG001SXZ12',
            'title' => "Used 2009 Sportscoach Legend RV  (500 HP-TG) w/4 Slides",
            'condition' => 0,
            'year' => 2009
        ]);

        factory(Rv::class)->create([
            'stock_number' => "XXSAX1",
            'title' => "New 2020 Coachmen Adrenaline F33A17 Toy Hauler W/5.5KW Gen, 2 A/Cs",
            'condition' => 1,
            'year' => 2020
        ]);
        factory(Rv::class)->create([
            'stock_number' => "mmmmsssx",
            'title' => "Some weird title that does not need to be found 1990",
            'condition' => 1,
            'year' => 1990
        ]);

        $uri = "/rvs";



        //****** new keyword **********//
        //   only year  //

        $keyword= "1990";
        $param_str = "filter[keyword]=$keyword&fields[rvs]=stock_number";

        $params = [];
        parse_str($param_str , $params);
        $response = $this->connect($uri , $params);
        $this->assertEquals(
            1,
            $response->json("meta.page.total")
        );

        //****** new keyword **********//

        $keyword= "new 2009 sportscoach";
        $param_str = "filter[keyword]=$keyword&fields[rvs]=stock_number";

        $params = [];
        parse_str($param_str , $params);
        $response = $this->connect($uri , $params);

        $this->assertEquals(
            $stock_number,
            $response->json("data")[0]["attributes"]["stock_number"]
        );
        $this->assertEquals(
            1,
            $response->json("meta.page.total")
        );



        //****** new keyword **********//

        $keyword= "2009 Sportscoac";
        $param_str = "filter[keyword]=$keyword&fields[rvs]=stock_number";

        $params = [];
        parse_str($param_str , $params);
        $response = $this->connect($uri , $params);

        $this->assertEquals(
            2,
            $response->json("meta.page.total")
        );




        //****** new keyword **********//

        $keyword= "new sportscoach";
        $param_str = "filter[keyword]=$keyword&fields[rvs]=stock_number";

        $params = [];
        parse_str($param_str , $params);
        $response = $this->connect($uri , $params);

        $this->assertEquals(
            $stock_number,
            $response->json("data")[0]["attributes"]["stock_number"]
        );

        //****** new keyword **********//

        $keyword= "coachmen used";
        $param_str = "filter[keyword]=$keyword&fields[rvs]=stock_number";

        $params = [];
        parse_str($param_str , $params);
        $response = $this->connect($uri , $params);

        $this->assertEquals(
            0,
            $response->json("meta.page.total")
        );



        //****** new keyword **********//
        //****** test partial stock_number **********//

        $keyword= "01SXZ1";
        $param_str = "filter[keyword]=$keyword&fields[rvs]=stock_number";

        $params = [];
        parse_str($param_str , $params);
        $response = $this->connect($uri , $params);

        $this->assertEquals(
            2 ,
            $response->json("meta.page.total")
        );


        //****** new keyword **********//
        //****** test full stock_number **********//

        $keyword= "MG001SXZ11";
        $param_str = "filter[keyword]=$keyword&fields[rvs]=stock_number";

        $params = [];
        parse_str($param_str , $params);
        $response = $this->connect($uri , $params);

        $this->assertEquals(
            $stock_number,
            $response->json("data")[0]["attributes"]["stock_number"]
        );





        //****** new keyword **********//
        //****** not found  **********//

        $keyword= "anything used";
        $param_str = "filter[keyword]=$keyword&fields[rvs]=stock_number";

        $params = [];
        parse_str($param_str , $params);
        $response = $this->connect($uri , $params);

        $this->assertEquals(
            0,
            $response->json("meta.page.total")
        );



    }

    /**
     * @test
     */
    public function can_search_between_years()
    {
        $total_number_year_2018 = 18;
        $total_number_year_2017 = 17;
        $total_number_year_2005 = 25;
        $total_number_year_2000 = 20;
        factory(Rv::class, $total_number_year_2018)->create([
            'year' => '2018'
        ]);
        factory(Rv::class, $total_number_year_2017)->create([
            'year' => '2017'
        ]);
        factory(Rv::class, $total_number_year_2000)->create([
            'year' => '2000'
        ]);
        factory(Rv::class, $total_number_year_2005)->create([
            'year' => '2005'
        ]);
        $uri_between_2000_and_2005 = "/rvs/?filter[year_gte]=2000&filter[year_lte]=2005";
        $url_for_2017_year =  "/rvs/?filter[year_gte]=2017&filter[year_lte]=2017";
        $uri_between_2000_and_2018 = "/rvs/?filter[year_gte]=2000&filter[year_lte]=2018";
        $uri_between_2017_and_2018 =  "/rvs/?filter[year_gte]=2017&filter[year_lte]=2018";
        $uri_one_year_gte =  "/rvs/?filter[year_gte]=2017";
        $uri_one_year_lte = "/rvs/?filter[year_lte]=2005";

        $response = $this->connect($uri_between_2000_and_2005);
        $this->assertEquals(
            $total_number_year_2000 + $total_number_year_2005,
            $response->json("meta.page.total")
        );
        $response = $this->connect($url_for_2017_year);
        $this->assertEquals(
            $total_number_year_2017,
            $response->json("meta.page.total")
        );
        $response = $this->connect($uri_between_2000_and_2018);
        $this->assertEquals(
            $total_number_year_2000 + $total_number_year_2005 + $total_number_year_2017 + $total_number_year_2018,
            $response->json("meta.page.total")
        );
        $response = $this->connect($uri_between_2017_and_2018);
        $this->assertEquals(
            $total_number_year_2017 + $total_number_year_2018,
            $response->json("meta.page.total")
        );
        $response = $this->connect($uri_one_year_gte);
        $this->assertEquals(
            $total_number_year_2017 + $total_number_year_2018,
            $response->json("meta.page.total")
        );
        $response = $this->connect($uri_one_year_lte);
        $this->assertEquals(
            $total_number_year_2005 + $total_number_year_2000,
            $response->json("meta.page.total")
        );
    }

    /**
     * @test
     */
    public function can_search_inventory_with_specific_year()
    {
        $total_number_year_2017 = 17;
        $total_number_year_2018 = 18;
        $total_number_year_2000 = 20;
        factory(Rv::class, $total_number_year_2018)->create([
            'year' => '2018'
        ]);
        factory(Rv::class, $total_number_year_2017)->create([
            'year' => '2017'
        ]);
        factory(Rv::class, $total_number_year_2000)->create([
            'year' => '2000'
        ]);
        $uri = "/rvs?filter[year]=2000";
        $response = $this->connect($uri);

        $this->assertEquals(
            $total_number_year_2000,
            $response->json("meta.page.total")
        );
    }
    /**
     * @test
     */
    public function can_filter_inventory_with_condition()
    {
        $total_number_new_rvs = 30;
        $total_number_used_rvs = 60;
        factory(Rv::class, $total_number_new_rvs)->create([
            'condition' => true
        ]);
        factory(Rv::class, $total_number_used_rvs)->create([
            'condition' => false
        ]);
        $uri = "/rvs?filter[condition]=1";
        $response = $this->connect($uri);
        $this->assertEquals(
            $total_number_new_rvs,
            $response->json("meta.page.total")
        );
    }

    /**
     * @test
     */
    public function can_filter_inventory_with_a_model_type_list()
    {
        $type_test_ids = [111, 5];
        $total_number_of_rvs_belong_to_the_test_types = 2;
        $total_number_of_rvs_dont_belong_to_test_types = 9;
        factory(Rv::class, $total_number_of_rvs_belong_to_the_test_types)->create([
            'type_id' => $type_test_ids[array_rand($type_test_ids)] // just random element from the array
        ]);

        factory(Rv::class, $total_number_of_rvs_dont_belong_to_test_types)->create([
            'type_id' => 114
        ]);

        $uri = "/rvs?filter[type_list]=" . implode('|' , $type_test_ids);
        $response = $this->connect($uri);
        $this->assertEquals(
            $total_number_of_rvs_belong_to_the_test_types,
            $response->json("meta.page.total")
        );
    }


    /**
     * @test
     */
    public function can_filter_inventory_with_a_type()
    {
        $type_test_id = 5;
        $total_number_of_rvs_belong_to_the_test_type = 5;
        $total_number_of_rvs_dont_belong_to_test_type = 6;
        factory(Rv::class, $total_number_of_rvs_belong_to_the_test_type)->create([
            'type_id' => $type_test_id
        ]);
        factory(Rv::class, $total_number_of_rvs_dont_belong_to_test_type)->create([
            'type_id' => 114
        ]);

        $uri = "/rvs?filter[type]=$type_test_id";
        $response = $this->connect($uri);
        $this->assertEquals(
            $total_number_of_rvs_belong_to_the_test_type,
            $response->json("meta.page.total")
        );
    }

    /**
     * @test
     */
    public function can_filter_inventory_with_a_model_list()
    {
        $model_test_ids = [222, 333];
        $total_number_of_rvs_belong_to_the_test_models = 3;
        $total_number_of_rvs_dont_belong_to_test_models = 7;
        factory(Rv::class, $total_number_of_rvs_belong_to_the_test_models)->create([
            'model_id' => $model_test_ids[array_rand($model_test_ids)] // just random element from the array
        ]);

        factory(Rv::class, $total_number_of_rvs_dont_belong_to_test_models)->create([
            'model_id' => 114
        ]);

        $uri = "/rvs?filter[model_list]=" . implode('|' , $model_test_ids);
        $response = $this->connect($uri);
        $this->assertEquals(
            $total_number_of_rvs_belong_to_the_test_models,
            $response->json("meta.page.total")
        );
    }

    /**
     * @test
     */
    public function can_filter_inventory_with_a_model()
    {
        $model_test_id = 105;
        $total_number_of_rvs_belong_to_the_test_model = 12;
        $total_number_of_rvs_dont_belong_to_test_model = 6;
        factory(Rv::class, $total_number_of_rvs_belong_to_the_test_model)->create([
            'model_id' => $model_test_id
        ]);
        factory(Rv::class, $total_number_of_rvs_dont_belong_to_test_model)->create([
            'model_id' => 114
        ]);

        $uri = "/rvs?filter[model]=$model_test_id";
        $response = $this->connect($uri);
        $this->assertEquals(
            $total_number_of_rvs_belong_to_the_test_model,
            $response->json("meta.page.total")
        );
    }

    /**
     * @test
     */
    public function can_filter_inventory_with_a_brand_list()
    {
        $brand_test_ids = [113, 255];
        $total_number_of_rvs_belong_to_the_test_brands = 7;
        $total_number_of_rvs_dont_belong_to_test_brand = 2;
        factory(Rv::class, $total_number_of_rvs_belong_to_the_test_brands)->create([
            'brand_id' => $brand_test_ids[array_rand($brand_test_ids)] // just random element from the array
        ]);

        factory(Rv::class, $total_number_of_rvs_dont_belong_to_test_brand)->create([
            'brand_id' => 114
        ]);

        $uri = "/rvs?filter[brand_list]=" . implode('|' , $brand_test_ids);
        $response = $this->connect($uri);
        $this->assertEquals(
            $total_number_of_rvs_belong_to_the_test_brands,
            $response->json("meta.page.total")
        );
    }



    /**
     * @test
     */
    public function can_filter_inventory_with_a_brand()
    {
        $brand_test_id = 113;
        $total_number_of_rvs_belong_to_the_test_brand = 7;
        $total_number_of_rvs_dont_belong_to_test_brand = 2;
        factory(Rv::class, $total_number_of_rvs_belong_to_the_test_brand)->create([
            'brand_id' => $brand_test_id
        ]);
        factory(Rv::class, $total_number_of_rvs_dont_belong_to_test_brand)->create([
            'brand_id' => 114
        ]);

        $uri = "/rvs?filter[brand]=$brand_test_id";
        $response = $this->connect($uri);
        $this->assertEquals(
            $total_number_of_rvs_belong_to_the_test_brand,
            $response->json("meta.page.total")
        );
    }


    /**
     * @test
     */
    public function can_filter_inventory_whether_sold_or_not()
    {
        $num_sold = 4;
        $num_not_sold = 2;
        factory(Rv::class, $num_sold)->state('sold')->create();
        factory(Rv::class, $num_not_sold)->state('not_sold')->create();

        $uri_sold = '/rvs/?filter[sold]=1';
        $uri_not_sold = '/rvs/?filter[sold]=0';

        //$sold_response = $this->connect($uri_sold);
        $not_sold_response = $this->connect($uri_not_sold);
       /* $this->assertEquals(
            $num_sold,
            $sold_response->json("meta.page.total")
        );*/
        $this->assertEquals(
            $num_not_sold,
            $not_sold_response->json("meta.page.total")
        );
    }

    /**
     * @test
     * @return void
     */
    public function can_get_a_title_of_a_single_rv_by_stock_number()
    {
        $title = "New Rv that runs really fast!";
        $stock_number = 'MG001SXZ11';
        factory(Rv::class)->create([
            'stock_number' => $stock_number,
            'title' => $title
        ]);


        $param_str = "filter[stock_number]=$stock_number&fields[rvs]=stock_number";
        $uri = "/rvs";

        $params = [];
        parse_str($param_str , $params);
        $response = $this->connect($uri , $params);

        $this->assertEquals(
            $stock_number,
            $response->json("data")[0]["attributes"]["stock_number"]
        );
    }

    /**
     * @test
     * Get title of a single rv...
     *
     * @return void
     */
    public function can_get_a_title_of_a_single_rv_by_id()
    {

        $title = "New Rv that runs really fast!";
        $id = 555;
        factory(Rv::class)->create([
            'id' => $id,
            'title' => $title
        ]);
        $uri = '/rvs/555';
        $response = $this->connect($uri);
        $this->assertEquals(
            $title,
            $response->json('data.attributes.title')
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
