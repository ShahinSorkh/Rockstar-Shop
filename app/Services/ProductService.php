<?php

namespace App\Services;

use App\Models\Product;

class ProductService
{
    public function getIndex()
    {
        return Product::with('customizations')->get();
    }
}
