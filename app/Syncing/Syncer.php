<?php

namespace App\Syncing;

use App\Attribute;
use App\Brand;
use App\Classification;
use App\Option;
use App\Rv;
use App\RvImage;
use App\RvModel;
use App\Syncing\Rvusa\Adaptor;
use App\Syncing\Rvusa\Inventory;
use App\Type;
use Illuminate\Support\Facades\DB;

class Syncer
{

    use InteractConsoleTrait;
    protected $toSyncData;
    protected $incremental_nam_per_request = 50;


    public function syncDeleted($is_sold)
    {
        $brands = Brand::onlyHasInventory()->get();
        $this->prepareProgressBar(count($brands));
        foreach ($brands as $brand){
            $this->tellInfo("  [Checking Brand : $brand->name ($brand->id)]");
            $filters['brand_id'] = $brand->id;
            $filters['is_sold'] = $is_sold;
            $this->syncDeletedBrand($filters);
            $this->progressBar();
        }
        $this->finishProgress();

    }
    public function sync($rvs_api)
    {
        if (isset($rvs_api['results']))
        {
            foreach ($rvs_api['results'] as $single_rv_api)
            {
                $adaptor = new Adaptor($single_rv_api);
                $rv_input = $adaptor->rv();
                $images = $adaptor->images();
                $options = $adaptor->options();
                $documents = $adaptor->documents();
                $classifications = $adaptor->classifications();

                $rv = Rv::find($rv_input['id']);
                if($rv)
                {
                    $rv->update($rv_input) ;
                    $rv->images()->delete();
                    $rv->documents()->delete();
                }
                else
                {
                    $rv = Rv::create($rv_input);
                }
                foreach ($images as $value){
                    $rv->images()->create($value);
                }
                foreach ($documents as $value){
                    $rv->documents()->create($value);
                }

                $this->createMissingBrand($single_rv_api);
                $this->createMissingModel($single_rv_api);
                $this->attachModelAndType($rv_input);
                $this->createMissingAttributes($adaptor->attributes());
                $this->createMissingOptions($options);
                $this->createMissingClassifications($classifications);

                $rv->attributes()->sync($adaptor->attributesIdsOnly());
                $rv->options()->sync($options);
                $rv->classifications()->sync($classifications);

            }
        }
    }

    /**
     * Sync Last
     */
    public function lastModified()
    {
        $rvusa_inventory_client = new Inventory();
        $rvs_api = $rvusa_inventory_client->last_modified()->get();
        $this->sync($rvs_api);
    }

    public function inventoryBrand($brand_id)
    {
        $filters = ['is_sold' => false , 'brand_id' => $brand_id];
        $this->incremental($filters);
    }

    public function allSold()
    {
        $filters = ['is_sold' => true];
        $this->goOverBrands($filters);
    }
    public function allInventory()
    {
        $filters = ['is_sold' => false];
        $this->goOverBrands($filters);
    }
    public function incremental($filters)
    {
            $curr_page = 1;
            $count = 0;
            $inventory_client = new Inventory();

            $inventory_client->setFilters($filters);
            do
            {
                $rvs_api = $inventory_client->get($this->incremental_nam_per_request , $curr_page);
                $this->sync($rvs_api);
                if(isset($rvs_api['results'])){
                    $count += count($rvs_api['results']);
                    $this->tellInfo("Synced $count after request $curr_page ");
                    $curr_page ++;
                }
            }  while(isset($rvs_api['results']));
    }
    private function goOverBrands($filters)
    {
        $brands = Brand::all();
        $this->prepareProgressBar(count($brands));
        foreach ($brands as $brand){
            $this->tellInfo("  [Syncing Brand : $brand->name ($brand->id)]");
            $filters['brand_id'] = $brand->id;
            $this->incremental($filters);
            $this->progressBar();
        }
        $this->finishProgress();
    }
    private function createMissingAttributes($attributes)
    {
        foreach ($attributes as $key => $value)
        {
            $attr = Attribute::find($value['id']);
            if(! $attr ){
                Attribute::create($value);
            }
        }
    }

    private function createMissingOptions($options)
    {
        foreach ($options as $key => $value)
        {
            $option = Option::find($value);
            if(! $option ){
                Option::create(['slug' => $value]);
            }
        }
    }

    private function createMissingClassifications($options)
    {
        foreach ($options as $key => $value)
        {
            $option = Classification::find($value);
            if(! $option ){
                Classification::create(['slug' => $value]);
            }
        }
    }


    private function createMissingModel($single_rv_api)
    {
        if (isset($single_rv_api['Model_ID']) && isset($single_rv_api['Brand_ID'])  && $single_rv_api['Model_ID']) {
            // For sure the rv from rvusa has a model!
            // First let's check if we have the same model too
            $model = RvModel::find($single_rv_api['Model_ID']);
            if (!$model) {
                // we don't have this model, let's create it!
                $model = new RvModel();
                $model->id = $single_rv_api['Model_ID'];
                $model->name = $single_rv_api['Model'];
                $model->brand_id = $single_rv_api['Brand_ID'];
                $model->save();
            }
        }

    }

    private function attachModelAndType($rv_input)
    {
        $type = Type::find($rv_input['type_id']);
        if($type && isset($rv_input['model_id']) && $rv_input['model_id'] ){
            $type->models()->sync([$rv_input['model_id']], false);
        }
    }

    private function createMissingBrand($single_rv_api)
    {
        $brand = Brand::find($single_rv_api['Brand_ID']);
        if (!$brand) {
            // we don't have this brand, let's create it!
            $brand = new Brand();
            $brand->id = $single_rv_api['Brand_ID'];
            $brand->name = $single_rv_api['Brand'];
            $brand->save();
        }
    }

    private function syncDeletedBrand($filters)
    {
        $rvusa_inventory_client = new Inventory();
        $rvs_api = $rvusa_inventory_client->setFilters($filters)->get();
        $rvs_database = Rv::where($filters)->get();

        // get the extra rvs in the database to delete
        $remove_rvs_array = $this->getExtraRvsFromDatabase($rvs_api , $rvs_database);
        if ( empty($remove_rvs_array ) )
            return;
        if (count($remove_rvs_array) > env('MAX_AUTO_SYNC_DELETE') ){
            $this->tellInfo("Auto syncing won't be completed: ". count($remove_rvs_array) . " is greater than ". env('MAX_AUTO_SYNC_DELETE'));
            return;
        }


        foreach ($remove_rvs_array as $id){
            $this->removeRv($id);
            $this->tellInfo("Rv id: {$id} has been removed");
        }
    }

    private function removeRv($id){
        DB::table('rv_images')->where('rv_id', '=', $id)->delete();
        DB::table('option_rv')->where('rv_id', '=', $id)->delete();
        DB::table('documents')->where('rv_id', '=', $id)->delete();
        DB::table('classification_rv')->where('rv_id', '=', $id)->delete();
        DB::table('attribute_rv')->where('rv_id', '=', $id)->delete();
        DB::table('rvs')->where('id', '=', $id)->delete();
    }
    private function getExtraRvsFromDatabase($rvs_api , $rvs_database)
    {

        $to_remove_rvs = array();

        foreach($rvs_database as $rv_database_array)
        {
            $flag_keep_rv_in_db = false;
            if(isset($rvs_api['results']))
            {
                foreach($rvs_api['results'] as $rv_api_array)
                {
                    if( $rv_database_array['id'] == $rv_api_array['ID'])
                    {
                        $flag_keep_rv_in_db = true;
                        break;
                    }

                }
            }


            if( ! $flag_keep_rv_in_db)
            {
                array_push($to_remove_rvs,$rv_database_array['id']) ;
            }
        }
        return $to_remove_rvs;
    }

}