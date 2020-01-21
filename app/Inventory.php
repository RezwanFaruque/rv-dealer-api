<?php

namespace App;

use Illuminate\Support\Facades\DB;

class Inventory
{

    protected $filters = [];
    protected $builder;
    public function __construct($filters = [])
    {
        // we're always interested in not sold rvs
        $this->filters['is_sold'] = false;
        if(count($filters) > 0 )
            $this->filters = array_merge($this->filters, $filters);

        $this->builder = DB::table('rvs');
    }

    public function typeModels($type_id)
    {
        $this->builder
            ->join('rv_models', 'rvs.model_id', '=' , 'rv_models.id')
            ->select(DB::raw('distinct(model_id), rv_models.name'))
            ->where('type_id', '=', $type_id);
        return $this;
    }


    /**
     *
     * get models and brands of specific classifications
     * @param $classifications
     * @return $this
     */
    public function modelsBrandsOfClassifications($classifications)
    {
        $this->builder
            ->join('rv_models', 'rvs.model_id', '=' , 'rv_models.id')
            ->join('brands' , 'rvs.brand_id' , '=' , 'brands.id')
            ->join('classification_rv' , 'rvs.id' , '=' , 'classification_rv.rv_id')

            ->select(DB::raw(
                'distinct(model_id) as model_id,
                 rv_models.name as model_name, 
                 brands.id as brand_id, brands.name as brand_name'
            ))
            ->whereIn('classification_rv.classification_slug', $classifications);
        return $this;
    }
    public function get()
    {
        foreach ($this->filters as $key=> $value)
        {
            $this->builder->where($key , $value);
        }
        return $this->builder->get();
    }
}
