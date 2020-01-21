<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RvImage extends Model
{
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];


     /**
     * This favorite units belongs to Rv image.
     */
    public function favoriteUnits(){

        return $this->belongsTo(RvImage::class,'rv_id','rv_id');
    }


    public function broughtUnit(){
        return $this->belongsTo(RvBuyer::class,'rv_id','rv_id');
    }
}
