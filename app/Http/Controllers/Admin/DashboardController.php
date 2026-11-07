<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\Subcategory;

class DashboardController extends Controller
{
    public function index()
    {
        return view('admin.dashboard.index', [
            'categoryCount' => Category::count(),
            'activeCategoryCount' => Category::where('is_active', true)->count(),
            'subcategoryCount' => Subcategory::count(),
            'productCount' => Product::count(),
            'activeProductCount' => Product::where('is_active', true)->count(),
            'inactiveProductCount' => Product::where('is_active', false)->count(),
            'orderCount' => Order::count(),
            'pendingOrderCount' => Order::where('status', 'pending')->count(),
        ]);
    }
}
