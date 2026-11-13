<?php

use Illuminate\Support\Facades\Route;
use RChuikov\LaravelAdvancedPagination\PaginationAssets;

Route::get(
    '/advanced-pagination/assets/{hash}/{file}',
    [PaginationAssets::class, 'show'],
)
    ->where('hash', '[a-f0-9]{16}')
    ->where('file', 'pagination\.(css|js)')
    ->name('advanced-pagination.asset');
