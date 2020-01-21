<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Statistic extends Model
{
    //
    protected $guarded = [];

    public static function sync()
    {
        $count_new = Rv::where('condition', 1)->where('is_sold' , 0)->count();
        $new = Statistic::firstOrNew(['slug' => 'new']);
        $new->count = $count_new;
        $new->save();

        $count_used = Rv::where('condition', 0)->where('is_sold' , 0)->count();
        $used = Statistic::firstOrNew(['slug' => 'used']);
        $used->count = $count_used;
        $used->save();

        $count_all = $count_new + $count_used;
        $all = Statistic::firstOrNew(['slug' => 'all']);
        $all->count = $count_all;
        $all->save();
    }
}
