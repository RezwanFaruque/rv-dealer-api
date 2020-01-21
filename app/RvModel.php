<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RvModel extends Model
{
    protected $guarded = ['id'];
    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * Rvs of the brand...
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function rvs()
    {
        return $this->hasMany(Rv::class,'model_id','id');
    }
    public function rvsNotSold()
    {
        return  $this->hasMany(Rv::class,'model_id','id')->where('is_sold', 0);
    }
    public function types()
    {
        return $this->belongsToMany(Type::class, 'model_type','model_id' ,'type_id');
    }
    public function Brand()
    {
        return $this->belongsTo(Brand::class);
    }
    public function rvsOfCondition($condition, $sold = 0)
    {
        return $this->hasMany(Rv::class,'model_id','id')->where('is_sold', $sold )->where('condition' , $condition);
    }
    public function scopeOnlyHasInventory($query)
    {
        return $query->where('count' , '>' , 0);
    }
}
