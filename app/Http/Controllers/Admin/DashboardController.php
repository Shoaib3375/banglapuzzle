<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\Subcategory;

class DashboardController extends Controller
{
    public function index()
    {
        return view('admin.dashboard.index', [
            'categoryCount'    => Category::count(),
            'subcategoryCount' => Subcategory::count(),
            'productCount'     => Product::count(),
        ]);
    }
}
