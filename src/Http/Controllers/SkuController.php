<?php

namespace DagaSmart\Sku\Http\Controllers;

use DagaSmart\Sku;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use DagaSmart\BizAdmin\Renderers\BaseRenderer;
use DagaSmart\BizAdmin\Controllers\AdminController;

class SkuController extends AdminController
{
    /**
     * 生成 sku
     *
     * @param Request $request
     *
     * @return JsonResponse|JsonResource
     */
    public function generate(Request $request)
    {
        $specGroup = $request->input('groups');

        if (is_null($specGroup)) {
            return $this->response()->success([]);
        }

        admin_abort_if(blank(Arr::flatten($specGroup)), '请填写规格组');

        $groupName = Arr::pluck($specGroup, 'group_name');

        $spec = collect($specGroup)->pluck('specs')->map(function ($item) {
            admin_abort_if(blank($item), '请填写规格值');

            return array_column($item, 'spec');
        })->toArray();

        // 规格交叉组合
        $specCrossJoin = Arr::crossJoin(...$spec);

        $groupNameMd5 = array_map(fn($item) => md5($item), $groupName);
        $value        = array_map(fn($item) => array_combine($groupNameMd5, $item), $specCrossJoin);
        $columns      = array_map(fn($item) => amis()->TableColumn(md5($item), $item), $groupName);

        if ($request->input('sku_columns')) {
            $columns = array_merge($columns, $request->input('sku_columns'));
        } else {
            $columns[] = amis()->NumberControl('price', '价格')
                ->value(0)
                ->min(0)
                ->max(999999999)
                ->precision(0)
                ->step(0.01)
                ->required()
                ->width(240);
            $columns[] = amis()->NumberControl('stock', '库存')
                ->value(0)
                ->min(0)
                ->max(999999999)
                ->step(1)
                ->required()
                ->width(240);
        }

        if ($request->static) {
            $columns = array_map(function ($item) {
                if ($item instanceof BaseRenderer) {
                    $item->set('static', true);
                } else {
                    $item['static'] = true;
                }
                return $item;
            }, $columns);
        }

        // 回显数据
        $goodsId = $request->input('goods_id');
        if (filled($goodsId)) {
            Sku::make()->mergeExistsData($goodsId, $value);
        }

        // 更改 name 否则 table 数据不会更新
        $schema = amis()->TableControl('skus_' . now()->getTimestampMs())
            ->id($request->sku_name . '_skus')
            ->needConfirm()
            ->className('pt-3')
            ->columns($columns)
            ->value($value);

        return $this->response()->success($schema);
    }
}
