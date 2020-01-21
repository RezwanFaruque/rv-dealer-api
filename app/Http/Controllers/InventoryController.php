<?php

namespace App\Http\Controllers;

use App\Brand;
use App\Inventory;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    const DIRECTIONS_MAP = [
        '+' => 'asc',
        '-' => 'desc'
    ];
    const DEFAULT_SORT_DIRECTION = 'asc';
    public function cms()
    {
        return [
            'brands' => \App\Brand::all(),
            'models' => \App\RvModel::all(),
            'types' => \App\Type::all(),
            'classifications' => \App\Classification::all(),
            'options' => \App\Option::all(),
            'attributes' => \App\Attribute::all(),
        ];
    }

    public function brands()
    {
        return \App\Brand::onlyHasInventory()->get();
    }
    public function types()
    {
        return \App\Type::onlyHasInventory()->get();
    }
    public function models()
    {
        return \App\RvModel::onlyHasInventory()->get();
    }

    public function brandModels($record, Request $request)
    {
        $include = $request->get('include');

        $brand = \App\Brand::find($record);
        $query = $brand->models()->onlyHasInventory();
        if($include){
            $query->with($include);
        }
        // sorting +name or -name
        if($sort = $request->get('sort')){
            $first_char = substr($sort, 0, 1);
            if($first_char == "+" || $first_char == '-'){
                $dir = self::DIRECTIONS_MAP[$first_char] ;
                $field = substr($sort , 1);
            }
            else{
                $dir = self::DEFAULT_SORT_DIRECTION;
                $field = $sort;
            }
            $query = $query->orderBy($field, $dir);
        }
        return [
            'self' => $brand,
            'data' => $query->get(),
        ];
    }

    public function classificationsModels($record, Request $request)
    {
        $filter = [];
        if($request->get('filter')){
            $filter = $request->get('filter');
        }
        $classifications = explode('|', urldecode($record));
        $client = new Inventory($filter);
        $data = $client->modelsBrandsOfClassifications($classifications)->get();
        return ['data' => $data];
    }
    /*public function typeModelsGeneric($record)
    {
        $type = \App\Type::find($record);
        $client = new Inventory();

        return [
            'self' => $type,
            'data' => $client->typeModels($record)->get()
        ];
    }*/

    // this is only response type that needs to take into account multiple main resources (in our case, types), so we should urldecode and explode the ids that were passed
    public function typeModels($record)
    {
        $typeIds = explode('|', urldecode($record));
        $types = \App\Type::find($typeIds);

        $newUnitsOfType = \App\Rv::whereIn('type_id', $typeIds)->where('condition', '=', '1')->get();
        $modelIds = array_values(array_unique($newUnitsOfType->pluck('model_id')->all()));
        $newModelsOfType = \App\RvModel::whereIn('id', $modelIds)->orderBy('name', 'ASC')->get();

        $brandIds = array_values(array_unique($newModelsOfType->pluck('brand_id')->all()));
        $brands = \App\Brand::find($brandIds);

        // need to filter by keyword here

        return [
            'types' => $types,
            'typeModels' => $newModelsOfType,
            'brands' => $brands,
        ];
    }
}
