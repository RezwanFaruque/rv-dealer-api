<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
    protected $guarded = ['id'];
    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;
    public function models()
    {
        return $this->hasMany(RvModel::class);
    }
    /**
     * Rvs of the brand...
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
    public function scopeOnlyHasInventory($query)
    {
        return $query->where('count' , '>' , 0);
    }
}
