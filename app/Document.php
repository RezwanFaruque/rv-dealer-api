<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Document extends Model
{

    protected $guarded = [];
    /**
     * Get the rv that owns the document.
     */
    public function rv()
    {
        return $this->belongsTo(Rv::class);
    }
}
