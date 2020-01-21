<?php

namespace App\JsonApi\Models;

use CloudCreativity\LaravelJsonApi\Eloquent\AbstractAdapter;
use CloudCreativity\LaravelJsonApi\Pagination\StandardStrategy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class Adapter extends AbstractAdapter
{

    /**
     * Mapping of JSON API attribute field names to model keys.
     *
     * @var array
     */
    protected $attributes = [];

    /**
     * Adapter constructor.
     *
     * @param StandardStrategy $paging
     */
    public function __construct(StandardStrategy $paging)
    {
        parent::__construct(new \App\RvModel(), $paging);
    }

    protected function rvs()
    {
        return $this->hasMany('rvs');
    }
    public function types()
    {
        return $this->hasMany('types');
    }
    protected function brand()
    {
        return $this->belongsTo();
    }
    /**
     * @param Builder $query
     * @param Collection $filters
     * @return void
     */
    protected function filter($query, Collection $filters)
    {
        if ($model_id = $filters->get('model_id')) {
            $query->where('rv_models.id', '=', $model_id);
        }

    }

}
