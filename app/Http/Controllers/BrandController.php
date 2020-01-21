<?php

namespace App\Http\Controllers;

use App\Brand;
use Illuminate\Http\Request;
use App\Http\Resources\Brand as BrandResource;

class BrandController extends Controller
{
    public function index()
    {
        return new BrandResource( Brand::all() );
    }
}
