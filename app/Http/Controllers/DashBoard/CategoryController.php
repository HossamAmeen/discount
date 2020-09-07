<?php

namespace App\Http\Controllers\DashBoard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
class CategoryController extends BackEndController
{
    public function __construct(Category $model)
    {
        parent::__construct($model);
    }
}
