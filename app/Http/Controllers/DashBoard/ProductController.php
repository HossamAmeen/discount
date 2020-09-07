<?php

namespace App\Http\Controllers\DashBoard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
class ProductController extends BackEndController
{
    public function __construct(Product $model)
    {
        parent::__construct($model);
    }
}
