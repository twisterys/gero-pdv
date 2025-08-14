<?php

namespace App\Http\Controllers\Api\pos\v1;

use App\Http\Controllers\Controller;
use App\Models\CategorieDepense;

class DepenseCategoryController extends Controller
{
    public function liste()
    {
        return CategorieDepense::get(['id as value','nom as label']);
    }
}
