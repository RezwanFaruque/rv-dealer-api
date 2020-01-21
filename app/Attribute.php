<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Attribute extends Model
{
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * The rvs that belong to the attribute.
     */
    public function rvs()
    {
        return $this->belongsToMany(Rv::class)->withTimestamps();
    }
}
