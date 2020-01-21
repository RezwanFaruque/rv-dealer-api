<?php


namespace App\Syncing\Rvusa;



class ApiConnector
{
    private $url = "http://www.dlrwebservice.com/InventoryServiceRV.asmx/GetInventoryDataXML";
    protected $crul;

    function __construct()
    {
        $this->crul = new Curl($this->url, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/x-www-form-urlencoded'
            ],
        ]);
    }

    /**
     * Call api and set the response
     * @param $filters
     * @return array
     */
    public function connect($filters)
    {
        $params = [
            'uuid' => 'B45B4D701EDF8886BC5EC9D57',
            'options' => '',
            'querystring' => '?' . (http_build_query($filters)),
            'form' => ''
        ];

        $this->crul->setOptions([
            CURLOPT_POSTFIELDS => http_build_query($params)
        ]);

        $xmlParser = new XMLParser();
        return ($this->ParseRvDataAPI($xmlParser->xml2Array($this->crul->getResponse())));
    }


    public function ParseRvDataAPI($feedData)
    {
        $data = array();

        if (!$feedData) {
            $data['request']['status'] = "Failure: couldn't get feed";
        } else {
            if (isset($feedData['Inventory']['Dealer']['Units']['Unit'])) {
                $feedData = $feedData['Inventory']['Dealer']['Units']['Unit'];
            } else {
                $data['request']['status'] = -1;
                $data['request']['message'] = "Sorry, we have no units like this online at this time.";
                return $data;
            }


            $data['request']['status'] = 1;

            if (!isset($feedData[0]))
                $feedData = array(0 => $feedData);

            $result_count = 0;
            foreach ($feedData as $key => $value) {
                $fields = $value;
                unset($fields['Images']['Image']);
                $floorplan_index = null;
                $images = [];


                if ($value['Images']['Image']) {
                    if (is_array($value['Images']['Image'])) {
                        $item = [];
                        $i = 0;
                        foreach ($value['Images']['Image'] as $k => $v) {
                            if (!is_array($v)) { // single image
                                $item['url'] = $v;
                                $item['order'] = (isset($value['Images']['Image'][$i . "_attr"]['order']))
                                    ? $value['Images']['Image'][$i . "_attr"]['order'] :
                                    null;
                                if (isset($value['Images']['Image'][$i . "_attr"]['is_floorplan'])) {
                                    if ($value['Images']['Image'][$i . "_attr"]['is_floorplan'] === 'True') {
                                        $item['is_floorplan'] = true;
                                    }
                                    else{
                                        $item['is_floorplan']  = false;
                                    }
                                }
                                $i++;
                                $images[] = $item;
                                continue;
                            }
                        }
                    } else {
                        $item = [];
                        $item['url'] = $value['Images']['Image'];
                        if (isset($value['Images']['Image_attr']['is_floorplan']) && $value['Images']['Image_attr']['is_floorplan'] === 'True') {
                            $item['is_floorplan'] = true;
                        }else{
                            $item['is_floorplan'] = false;
                        }
                        $images[] = $item;
                    }


                }

                $fields['Images'] = $images;
                $fields['title'] = $fields['Condition'];
                $fields['Documents'] =  isset($value['Documents']) ? $value['Documents'] : array();

                if ($fields['Year'])
                    $fields['title'] .= ' ' . $fields['Year'];

                if ($fields['Brand'])
                    $fields['title'] .= ' ' . $fields['Brand'];

                if ($fields['Model'])
                    $fields['title'] .= ' ' . $fields['Model'];

                if($value['Floorplan']){
                    $fields['title'] .= ' ' . $fields['Floorplan'];
                }
                if($fields['Headline']){
                    $fields['title'] .= ' ' . $fields['Headline'];
                }
                $fields['title'] = html_entity_decode($fields['title']);
                foreach ($fields as $key => $value) {
                    if (is_array($value)) {
                        if (count($value) == 0)
                            $fields[$key] = '';
                    }
                }
                $data['results'][] = $fields;
                //$data['results'][ $typeTitle ][] = $result;

                //ItemDebug( $fields );
                $result_count++;
            }

            $data['request']['result_count'] = $result_count;
        }

        return $data;
    }
}