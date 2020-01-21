<?php


namespace App\Syncing\Rvusa;


use phpDocumentor\Reflection\Types\Self_;

class Inventory
{
    protected $api_time_zone;
    protected $api_date_format;
    protected $filters;

    CONST LARGE_NUMBER = 9999999;
    public function __construct()
    {
        $this->api_time_zone = "America/New_York";
        $this->api_date_format = "n/j/Y g:i A";
        // don't show hidden items
        $this->filters['show_hidden_units_id'] = 1;
    }

    public function setFilters ($filters)
    {
        foreach($filters as $key => $value) {
           if($key == "is_sold" ) {
               // attributeYes = 1 get only sold items
               // attributeNO = 1 ( just not sold items ) ; default inventory
               (isset($filters[$key]) && $filters[$key]) ?
                   $this->filters['attributeYes'] = 1 :
                   $this->filters['attributeNo']  = 1 ;
           }
           $this->filters[$key] = $value;
        }

        return $this;
    }
    public function get($num_unites = self::LARGE_NUMBER , $curr_page = null)
    {
        $this->filters['num_units'] = $num_unites;
        if(! is_null($curr_page) ){
            $this->filters['curr_page'] = $curr_page;
        }

        $api_connector_client = new ApiConnector();
        return $api_connector_client->connect($this->filters);
    }

    public function last_modified($time_interval = "-30 minutes")
    {
        $last_modified_date = new \DateTime($time_interval, new \DateTimeZone($this->api_time_zone) );
        $this->filters['date_last_updated_start'] = $last_modified_date->format($this->api_date_format);
        return $this;
    }
    
}