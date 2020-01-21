<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RvBuyer extends Model
{

    protected $table = 'rv_buyers';

    public function rv(){
        return $this->belongsTo(Rv::class,'rv_id','id');
    }


    public function rvImages(){
        return $this->hasMany(RvImage::class,'rv_id','rv_id');
    }
}
