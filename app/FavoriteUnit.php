<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FavoriteUnit extends Model
{
    protected $table = 'favorite_unites';


    /**
     * The rv belongs to Favorite Units.
     */
    public function rvs(){
        
        return $this->belongsTo(Rv::class,'rv_id','id');
    }

    /**
     * Favorite Units has Many Rv Images.
     */
    public function rv_Images(){

        return $this->hasMany(RvImage::class,'rv_id','rv_id');
    }
}
