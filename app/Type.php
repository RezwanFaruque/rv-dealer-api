<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Type extends Model
{
    public $incrementing = false;
    protected $guarded = ['id'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function rvs()
    {
        return $this->hasMany(Rv::class);
    }
    public function rvsNotSold()
    {
        return $this->hasMany(Rv::class)->where('is_sold', 0);
    }

    public function rvsOfCondition($condition, $sold = 0)
    {
        return $this->hasMany(Rv::class)->where('is_sold', $sold)->where('condition' , $condition);
    }

    public function models()
    {
        return $this->belongsToMany(RvModel::class, 'model_type','type_id' ,'model_id');
    }
    public function scopeOnlyHasInventory($query)
    {
        return $query->where('count' , '>' , 0);
    }
}
