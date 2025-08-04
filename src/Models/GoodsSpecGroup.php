<?php

namespace DagaSmart\Sku\Models;

use DagaSmart\BizAdmin\Models\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;

class GoodsSpecGroup extends BaseModel
{
    use SoftDeletes;

    protected static $unguarded = true;

    public function specs()
    {
        return $this->hasMany(GoodsSpec::class, 'group_id', 'id');
    }

    public function goods()
    {
        return $this->belongsTo(Goods::class);
    }
}
