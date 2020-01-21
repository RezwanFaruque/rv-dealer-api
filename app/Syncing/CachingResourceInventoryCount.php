<?php

namespace App\Syncing;

use App\Brand;
use App\RvModel;
use App\Type;
use Illuminate\Database\Eloquent\Model;

class CachingResourceInventoryCount
{
    use InteractConsoleTrait;
    const NEW = 1;
    const USED = 0;

    private function cache($resource)
    {

        $count_new = count($resource->rvsOfCondition(self::NEW)->get());
        $count = count($resource->rvsNotSold()->get());
        $resource->update([
            'count_new' => $count_new,
            'count' => $count
        ]);

        $this->tellInfo("Updated where count_new: $count_new, count: $count ");
    }
    public function types()
    {
        $types = Type::all();
        $this->prepareProgressBar(count($types));
        foreach ($types as $type)
        {
            $this->tellInfo("  [Consulting inventory counts for Type : $type->name ($type->id)]");
            $this->cache($type);
            $this->progressBar();
        }
    }
    public function brands()
    {
        $brands = Brand::all();
        $this->prepareProgressBar(count($brands));
        foreach ($brands as $brand)
        {
            $this->tellInfo("  [Consulting inventory counts for Brand : $brand->name ($brand->id)]");
            $this->cache($brand);
            $this->progressBar();
        }
    }
    public function models()
    {
        $models = RvModel::all();
        $this->prepareProgressBar(count($models));
        foreach ($models as $model)
        {
            $this->tellInfo("  [Consulting inventory counts for Model : $model->name ($model->id)]");
            $this->cache($model);
            $this->progressBar();
        }
    }

}