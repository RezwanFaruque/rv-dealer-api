<?php


namespace App\Syncing\Rvusa;


use App\Rv;
use Illuminate\Support\Facades\Schema;
use mysql_xdevapi\Exception;

class Adaptor
{
    protected $input_data;
    protected $cleaned_data = [];
    protected $attributes = [];
    protected $attributes_ids = [];
    protected $options = [];
    protected $classifications = [];

    /**
     * Adaptor constructor.
     * @param $data
     */
    public function __construct($data)
    {
        $this->input_data = $data;
        $this->prepareAttributes();
        $this->correctKeys();
        $this->correctValues();
    }

    protected function correctValues()
    {
        foreach ($this->cleaned_data as $key => $value){

            $value = trim($value);
            $this->cleaned_data[$key] = $value;
            $this->removeIfHasNoValue($key, $value);
            $this->convertBooleanStrings($key, $value);
        }
        $this->cleaned_data['condition'] = ($this->input_data['Condition'] == "New") ? 1 : 0;
        $this->cleaned_data['monthly_payment'] = filter_var($this->cleaned_data['monthly_payment'] , FILTER_SANITIZE_NUMBER_FLOAT);
        $this->cleaned_data['unit'] = $this->input_data['Length'] * 12 + $this->input_data['Length_Inches'];
        $this->cleaned_data['saving'] = null;
        $this->cleaned_data['sale_saving'] = null;
        $this->cleaned_data['discount'] = null;
        $this->cleaned_data['sale_discount'] = null;


        if($this->input_data['MSRP'] && $this->input_data['Price'] && $this->input_data['MSRP'] !== '0.00' ){
            $this->cleaned_data['saving'] = $this->input_data['MSRP'] - $this->input_data['Price'];
            $this->cleaned_data['discount']  = round($this->cleaned_data['saving'] / $this->input_data['MSRP'] * 100 );
        }
        if($this->input_data['MSRP'] && $this->input_data['Price_Field'] && $this->input_data['MSRP'] !== '0.00'){
            $this->cleaned_data['sale_saving'] = $this->input_data['MSRP'] - $this->input_data['Price_Field'];
            $this->cleaned_data['sale_discount']  = round($this->cleaned_data['sale_saving'] / $this->input_data['MSRP'] * 100 );
        }

        foreach ($this->input_data['Images'] as $image )
        {
            if(isset($image['is_floorplan']) && $image['is_floorplan'] ){
                $this->cleaned_data['floorplan_image'] = $image['url'];
            }
        }
    }


    protected function getLocalKey($key)
    {
        return strtolower($key);
    }
    /**
     * Get the local version of the rv data...
     * @return array
     */
    public function rv()
    {
        return $this->cleaned_data;
    }
    public function images()
    {
        return $this->input_data['Images'];
    }
    public function attributesIdsOnly()
    {
        return $this->attributes_ids;
    }
    public function attributes()
    {
        return $this->attributes;
    }
    public function options()
    {
        if(! empty($this->input_data['Options']) && is_array($this->input_data['Options'])){
            foreach ($this->input_data['Options'] as $key => $value)
            {
                $this->options[] = $key;
            }
        }
        return $this->options;
    }
    public function classifications()
    {
        if(! empty($this->input_data['Classifications']) && is_array($this->input_data['Classifications'])){
            foreach ($this->input_data['Classifications'] as $key => $value)
            {
                $this->classifications[] = $key;
            }
        }
        return $this->classifications;
    }

    protected function prepareAttributes()
    {
        /*
         *
         XML parser for attributes can be in 2 forms

        case 1:
         "Attributes" => array:2 [
                "Attribute" => []
                "Attribute_attr" => array:2 [
                  "id" => "6"
                  "name" => "Consignment"
                ]
              ]

        case : 2
        "Attributes" => array:1 [
            "Attribute" => array:4 [
              0 => []
              1 => []
              "0_attr" => array:2 [
                "id" => "6"
                "name" => "Consignment"
              ]
              "1_attr" => array:2 [
                "id" => "19"
                "name" => "Sold - Sale Pending"
              ]
            ]
          ]
         */

        if( isset($this->input_data['Attributes']['Attribute'])) {
            if (is_array($this->input_data['Attributes']['Attribute']) &&  count($this->input_data['Attributes']['Attribute'] ) > 0){
                foreach ($this->input_data['Attributes']['Attribute'] as $key => $value) {
                    if(strpos($key,'attr') !== false )
                    {
                        $this->attributes_ids[] = $value['id'];
                        $this->attributes[] = $value;
                    }
                }
            }
            elseif (
                isset($this->input_data['Attributes']['Attribute_attr']) &&
                is_array($this->input_data['Attributes']['Attribute_attr']) &&
                count($this->input_data['Attributes']['Attribute_attr']) > 0) {
                $this->attributes_ids[] = $this->input_data['Attributes']['Attribute_attr']['id'];
                $this->attributes[] = $this->input_data['Attributes']['Attribute_attr'];

            }
        }
    }
    public function documents()
    {
        $documents = [];
        if(isset($this->input_data['Documents']['Document']))
        {
            foreach ($this->input_data['Documents']['Document'] as $key => $value)
            {
                // if Title is empty array, reset value
                if(empty($value)){
                    $this->input_data['Documents']['Document'][$key]= null;
                }
                // we have multiple documents.
                if(is_array($this->input_data['Documents']['Document'][$key])){
                    foreach ($this->input_data['Documents']['Document'][$key] as $doc_key => $doc_value){
                        // if Title is empty array, reset value
                        if(empty($doc_value)){
                            $this->input_data['Documents']['Document'][$key][$doc_key]= null;
                        }
                    }
                    $documents[] = array_change_key_case($this->input_data['Documents']['Document'][$key], CASE_LOWER);
                }
                else
                {
                    $documents[] = array_change_key_case($this->input_data['Documents']['Document'], CASE_LOWER);
                    break;
                }
            }
        }


        // save global brochure
        if(isset($this->input_data['Brochures']['Brochure']['URL']))
        {
            $documents[] = [
                "title" =>  isset( $this->input_data['Brochures']['Brochure']['Title'] ) ? $this->input_data['Brochures']['Brochure']['Title'] : "",
                "category" => "Global Brochure",
                "url" => $this->input_data['Brochures']['Brochure']['URL']
            ];
        }

       return $documents;
    }


    public function correctKeys()
    {
        $rvs_columns = Schema::getColumnListing(app(Rv::class)->getTable());
        foreach ($this->input_data as $key=> $value)
        {
            $local_key = $this->getLocalKey($key);

            if( in_array($local_key, $rvs_columns) ){
                $this->cleaned_data[$local_key] = $value;
            }
        }
    }

    private function removeIfHasNoValue($key, $value)
    {
        $rvs_columns = Schema::getColumnListing(app(Rv::class)->getTable());
        if( empty($value) ){
            // we don't have it in database columns table.
            if( ! in_array($key, $rvs_columns)){
                unset($this->cleaned_data[$key]);
            }
            else{
                $this->cleaned_data[$key] = null;
            }
        }
    }

    private function convertBooleanStrings($key, $value)
    {
        if($value == 'True'){
            $this->cleaned_data[$key] = true;
        }
        else if($value == 'False'){
            $this->cleaned_data[$key] = false;

        }
    }


}