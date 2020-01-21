<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Rv extends Model
{
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];



    protected $table = 'rvs';
    /**
     * Rv belong to one brand.
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }
    /**
     * Rv Belongs to a type
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function type()
    {
        return $this->belongsTo(Type::class);
    }
    /**
     * Rv belongs to a model.
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function model()
    {
        return $this->belongsTo(RvModel::class,'model_id','id');
    }

    /**
     * Get the images for the rv.
     */
    public function images()
    {
        return $this->hasMany(RvImage::class);
    }

    /**
     * The attributes that belong to the rv.
     */
    public function attributes()
    {
        return $this->belongsToMany(Attribute::class)->withTimestamps();
    }

    /**
     * The options that belong to the rv.
     */
    public function options()
    {
        return $this->belongsToMany(Option::class)->withTimestamps();
    }

    /**
     * The classifications that belong to the rv.
     */
    public function classifications()
    {
        return $this->belongsToMany(Classification::class)->withTimestamps();
    }

    /**
     * The documents that belong to the rv.
     */
    public function documents()
    {
        return $this->hasMany(Document::class);
    }

    /**
     * The favorite unis hasMany rv.
     */
    public function favoriteUnits(){
        
        return $this->hasMany(FavoriteUnit::class,'rv_id','id');
    }


    public function broughtUnits(){
        return $this->hasMany(RvBuyer::class,'rv_id','id');
    }

     
}
