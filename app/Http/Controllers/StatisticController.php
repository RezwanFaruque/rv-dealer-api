<?php

namespace App\Http\Controllers;

use App\Statistic;
use Illuminate\Http\Request;

class StatisticController extends Controller
{
    public function index()
    {
        return Statistic::all();
    }
    public function show($slug)
    {
        return Statistic::where('slug' , $slug)->get();
    }

}
