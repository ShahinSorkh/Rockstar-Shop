<?php

namespace App\Http\Controllers;

use App\Http\Resources\MenuResource;
use App\Http\Resources\MenuResourceCollection;
use App\Services\ProductService;
use Illuminate\Http\Request;

class ProductController extends Controller
{

    private ProductService $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

    public function menuIndex()
    {
        return MenuResource::collection($this->productService->getIndex());
    }
}
