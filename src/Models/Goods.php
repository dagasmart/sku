<?php

namespace DagaSmart\Sku\Models;

use DagaSmart\BizAdmin\Models\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;

class Goods extends BaseModel
{
    use SoftDeletes;

    public function specGroups()
    {
        return $this->hasMany(GoodsSpecGroup::class);
    }

    public function specs()
    {
        return $this->hasMany(GoodsSpec::class);
    }

    public function skus()
    {
        return $this->hasMany(GoodsSku::class);
    }
}
