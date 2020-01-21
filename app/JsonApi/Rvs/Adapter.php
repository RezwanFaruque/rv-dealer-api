<?php

namespace App\JsonApi\Rvs;

use CloudCreativity\LaravelJsonApi\Eloquent\AbstractAdapter;
use CloudCreativity\LaravelJsonApi\Pagination\StandardStrategy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class Adapter extends AbstractAdapter
{
    /**
     * Force pagination by enabling default page size
     * @var array
     */
    protected $defaultPagination = ['number' => 1 , 'size' => 50];


    /**
     * Mapping of JSON API attribute field names to model keys.
     *
     * @var array
     */
    protected $attributes = [];

    /**
     * Adapter constructor.
     *
     * @param StandardStrategy $paging
     */
    public function __construct(StandardStrategy $paging)
    {
        parent::__construct(new \App\Rv(), $paging);
    }

    /**
     * @return \CloudCreativity\LaravelJsonApi\Eloquent\BelongsTo
     */
    protected function model()
    {
        return $this->belongsTo();
    }
    /**
     * @return \CloudCreativity\LaravelJsonApi\Eloquent\BelongsTo
     */
    protected function brand()
    {
        return $this->belongsTo();
    }

    /**
     * @return \CloudCreativity\LaravelJsonApi\Eloquent\BelongsTo
     */
    protected function type()
    {
        return $this->belongsTo();
    }
    /**
     * @return \CloudCreativity\LaravelJsonApi\Eloquent\HasMany
     */
    protected function options()
    {
        return $this->hasMany();
    }

    /**
     * @return \CloudCreativity\LaravelJsonApi\Eloquent\HasMany
     */
    protected function images()
    {
        return $this->hasMany();
    }

    /**
     * @return \CloudCreativity\LaravelJsonApi\Eloquent\HasMany
     */
    protected function classifications()
    {
        return $this->hasMany();
    }
    /**
     * @return \CloudCreativity\LaravelJsonApi\Eloquent\HasMany
     */
    protected function attributes()
    {
        return $this->hasMany();
    }
    /**
     * @return \CloudCreativity\LaravelJsonApi\Eloquent\HasMany
     */
    protected function documents()
    {
        return $this->hasMany();
    }
    protected function sort($query, array $sortBy)
    {
        if(request()->group_by_type ||
            ( isset( request()->get('filter')['group_by_type'])
                && request()->get('filter')['group_by_type'] == 1 )){
            $query->join('types' , 'rvs.type_id' , '=' , 'types.id');
            $query->orderBy('types.order', 'asc');
        }
        foreach ($sortBy as $sortParameter)
        {
            $direction = ($sortParameter->isAscending()) ? 'asc' : 'desc';
            $field = preg_replace('/\s+/', '', $sortParameter->getField());
            if($field == "price"){
                $field = "price_field";
            }
            $query->orderBy( $field , $direction);
        }
    }

    /**
     * @param Builder $query
     * @param Collection $filters
     * @return void
     */
    protected function filter($query, Collection $filters)
    {
        $columns = ['rvs.id as id', 'rvs.brand_id', 'rvs.model_id' , 'rvs.type_id' ,  'rvs.created_at', 'rvs.updated_at' ];
        // usually relationships, we will ignore them as they are not simple select fields.
        $ignored_selects = [
            'options',
            'brand',
            'images',
            'classifications',
            'type',
            'model',
            'attributes',
            'documents'
        ];

        if(isset(request()->all()['fields']['rvs']) ){
            $fields = explode(',', request()->all()['fields']['rvs'] );
            foreach ($fields as $field){
                if(! in_array($field, $ignored_selects) ){
                    array_push($columns, "rvs." . $field);
                }
            }
        }

        $query->select( $columns);


        if($option = $filters->get('option')){
            $query->addSelect([
             'option_rv.option_slug'
             ]);
            $query->join('option_rv', 'rvs.id', '=', 'option_rv.rv_id');
            $query->where('option_rv.option_slug' , '=' , $option);
        }
        if($options = $filters->get('options')){

            $unmodifiedOptions = $options;
            // remove Theater_Seating from options array, it's a special case
            if(in_array("Theater_Seating" , $options)){
                if (($key = array_search("Theater_Seating", $options)) !== false) {
                    unset($options[$key]);
                }
                if (($key = array_search("Recliner", $options)) !== false) {
                    unset($options[$key]);
                }
            }

            $ids = [];

            if(! empty($options)){
                // ids of rvs that match and satisfy all the options together
                $ids = DB::table('option_rv')
                    ->select('rv_id')
                    ->whereIn('option_rv.option_slug', $options)
                    ->groupBy('rv_id')
                    ->havingRaw("count(*) = ". count($options))
                    ->get()->pluck('rv_id');
            }

            if(in_array("Theater_Seating" , $unmodifiedOptions)){
                $theatre_recliner_ids = DB::table('option_rv')
                    ->select('rv_id')
                    ->distinct()
                    ->where('option_rv.option_slug', 'Theater_Seating')
                    ->orWhere('option_rv.option_slug', 'Recliner')
                    ->get()->pluck('rv_id');
                $ids = empty($ids) ? $theatre_recliner_ids : array_intersect( $ids->toArray() , $theatre_recliner_ids->toArray());
            }


            $query->whereIn('rvs.id', $ids );
        }

        if( $filters->get('attribute') || $filters->get('attributes') ){
            $query->addSelect([
                'attribute_rv.attribute_id'
            ]);
            $query->join('attribute_rv', 'rvs.id', '=', 'attribute_rv.rv_id');
        }

        if($attribute = $filters->get('attribute')){
            $query->where('attribute_rv.attribute_id' , '=' , $attribute);
        }
        if($attributes = $filters->get('attributes')){
            $query->whereIn('attribute_rv.attribute_id' , $attributes);
        }
        if ($fp_id = $filters->get('fp_id')) {
            $query->where('rvs.fp_id', '=', $fp_id);
        }
        if ($fuel_type = $filters->get('fuel_type')) {

            $query->where('rvs.fuel_type', '=', $fuel_type);
        }
        if( $filters->get('classification') || $filters->get('classifications') ){
            $query->addSelect([
                'classification_rv.classification_slug'
            ]);
            $query->join('classification_rv', 'rvs.id', '=', 'classification_rv.rv_id');
        }
        if($classification = $filters->get('classification')){
            $query->where('classification_rv.classification_slug' , '=' , $classification);
        }
        if($classifications = $filters->get('classifications')){
            $query->whereIn('classification_rv.classification_slug' , $classifications);
        }

        if ($brand_id = $filters->get('brand')) {
            $query->where('rvs.brand_id', '=', $brand_id);
        }
        if ($keyword = $filters->get('keyword')) {
            $query->where(function ($query) use ($keyword) {
                $originalKeyword = $keyword;
                // trying to detect condition
                $keyword = strtolower($keyword);
                $condition = null;
                if( strpos($keyword, 'used') !== false  ){
                    // keyword has used, let's set condition to 0
                    $condition = 0;
                    $keyword = str_replace("used", "" , $keyword);
                }
                elseif(strpos($keyword, 'new') !== false){
                    $condition = 1;
                    $keyword = str_replace("new", "" , $keyword);
                }
                if(! is_null($condition)){
                    $query->where('rvs.condition' , $condition);
                }

                // trying to detect year
                if (preg_match('/\b\d{4}\b/', $keyword, $matches)) {
                    $year = $matches[0];
                    $query->where('rvs.year' , '=' ,  $year);
                    $keyword = str_replace($year, "" , $keyword);
                }

                // remove whitespaces
                $keyword = trim($keyword, " ");
                $keyword = ltrim($keyword, " ");


                if( ! empty($keyword ) ){
                    $words = explode(" ", $keyword);
                    $counter = 0;
                    foreach ($words as $word){
                        // searching for class c for example
                        if( in_array($word, $this->potentialMultipleWords())
                            && isset($words[$counter + 1 ])
                            && in_array($words[$counter + 1] , $this->potentialContinuationWords())
                        ){
                            $word .= " " . $words[$counter + 1 ];
                        }
                        else if( in_array($word , $this->potentialContinuationWords())
                            || in_array($word, $this->ignoredKeywords() )){
                            continue;
                        }
                        $query->where('rvs.title', 'like', "%$word%");
                        $counter ++;
                    }
                }



                // or we search against stock number
                $query->orWhere('stock_number', 'like', "%$originalKeyword%");

            });
        }
        if ($brand_list = $filters->get('brand_list')) {
            $brand_array = explode("|" , $brand_list);
            $query->whereIn('rvs.brand_id', $brand_array);
        }
        if ($model_list= $filters->get('model_list')) {
            $model_array = explode("|" , $model_list);
            $query->whereIn('rvs.model_id', $model_array);
        }
        if ($type_list = $filters->get('type_list')) {
            $type_array = explode("|" , $type_list);
            $query->whereIn('rvs.type_id', $type_array);
        }

        if ($model_id = $filters->get('model')) {
            $query->where('rvs.model_id', '=', $model_id);
        }
        if ($type_id = $filters->get('type')) {
            $query->where('rvs.type_id', '=', $type_id);
        }
        if ($year = $filters->get('year')) {
            $query->where('rvs.year', '=', $year);
        }
        if ($year_gte = $filters->get('year_gte')) {
            $query->where('rvs.year', '>=', $year_gte);
        }
        if ($year_lte = $filters->get('year_lte')) {
            $query->where('rvs.year', '<=', $year_lte);
        }
        if ($price_gte = $filters->get('price_gte')) {
            $query->where('rvs.price_field', '>=', $price_gte);
        }
        if ($price_lte = $filters->get('price_lte')) {
            $query->where('rvs.price_field', '<=', $price_lte);
        }
        if ($discount_lte = $filters->get('discount_lte')) {
            $query->where('rvs.discount', '<=', $discount_lte);
        }
        if ($discount_gte = $filters->get('discount_gte')) {
            $query->where('rvs.discount', '>=', $discount_gte);
        }

        if ($stock_number = $filters->get('stock_number')) {
            $query->where('rvs.stock_number', '=', $stock_number);
        }
        if (! is_null( $filters->get('sold') ) ) {
            $query->where('rvs.is_sold', '=', $filters->get('sold'));
        }


        if (! is_null( $filters->get('is_consignment') ) ) {
            $query->where('rvs.is_consignment', '=', $filters->get('is_consignment'));
        }
        if (! is_null( $filters->get('condition') ) ) {
            $condition = filter_var($filters->get('condition'), FILTER_VALIDATE_BOOLEAN);
            $query->where('rvs.condition', '=', $condition );
        }
        if ($ids = $filters->get('ids')) {
            $ids_array = explode("|" , $ids);
            $query->whereIn('rvs.id', $ids_array);
        }

        //dd($query->toSql());
    }


    /**
     * List of words that should be ignored and not being search against title
     *
     * @return array
     */
    private function ignoredKeywords()
    {
        return ['rv'];
    }
    /**
     * Example word class indicates the next word could be joined with it
     * like class a, class b etc...
     */
    private function potentialMultipleWords()
    {
       return [
           'class'
       ];
    }

    private function potentialContinuationWords()
    {
        return ['a' , 'b', 'c' , 'b+'];
    }
}
