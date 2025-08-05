<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {

    protected $connection = 'biz';

    private string $table = 'basic_goods_skus';

    /**
     * 执行迁移
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        if (!Schema::hasTable($this->table)) {
            //创建表
            Schema::create($this->table, function (Blueprint $table) {
                $table->comment('基础商品sku表');
                $table->id();
                $table->integer('goods_id')->default(0)->comment('商品id');
                $table->string('spec_ids')->comment('规格id(逗号分隔,升序)');
                $table->decimal('price')->default(0)->comment('价格');
                $table->decimal('stock')->default(0)->comment('库存');
                $table->text('sku_json')->comment('sku数据');
                $table->timestamps();
                $table->softDeletes();
            });
        }
    }

    /**
     * 迁移回滚
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        if (Schema::hasTable($this->table)) {
            //检查是否存在数据
            $exists = DB::table($this->table)->exists();
            //不存在数据时，删除表
            if (!$exists) {
                //删除 reverse
                Schema::dropIfExists($this->table);
            }
        }


    }
};
