<?php

use DagaSmart\Sku\Http\Controllers;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'sku'], function () {
    Route::post('generate', [Controllers\SkuController::class, 'generate']);
});
