<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Product;
use App\Models\OrderDetail;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // 1. Lấy 5 sản phẩm bán chạy nhất (dựa vào tổng số lượng bán được)
        $topProducts = Product::select('products.*', DB::raw('SUM(order_details.orderQuantity) as total_sold'))
            ->join('product_details', 'products.productID', '=', 'product_details.prdID')
            ->join('order_details', 'product_details.id', '=', 'order_details.productDetailID')
            ->groupBy('products.productID')
            ->orderByDesc('total_sold')
            ->with('firstImage')
            ->take(5)
            ->get();

        // 2. Lấy 5 đơn hàng gần đây nhất
        $recentOrders = Order::with(['customer', 'status'])
            ->orderByDesc('created_at')
            ->take(5)
            ->get();

        $currentMonth = Carbon::now()->month;
        $lastMonth = Carbon::now()->subMonth()->month;

        // Tổng doanh thu tháng này
        $currentRevenue = Order::whereMonth('created_at', $currentMonth)
            ->where('staID', 4)
            ->sum('totalPrice');

// Tổng doanh thu tháng trước (chỉ tính đơn đã giao hàng)
        $lastRevenue = Order::whereMonth('created_at', $lastMonth)
            ->where('staID', 4)
            ->sum('totalPrice');

        // Tổng số đơn hàng tháng này
        $currentOrders = Order::whereMonth('created_at', $currentMonth)->count();
        $lastOrders = Order::whereMonth('created_at', $lastMonth)->count();

        // Tổng số sản phẩm và sản phẩm mới tháng này
        $totalProducts = Product::count();
        $newProducts = Product::whereMonth('created_at', $currentMonth)->count();

        // Tổng số khách hàng và khách mới tháng này
        $totalCustomers = User::where('role', 'customer')->count();
        $newCustomers = User::where('role', 'customer')->whereMonth('created_at', $currentMonth)->count();
        $lastCustomers = User::where('role', 'customer')->whereMonth('created_at', $lastMonth)->count();

        return view('AdminPage.Dashboard', [
            'currentRevenue' => $currentRevenue,
            'revenueChange' => $lastRevenue > 0 ? (($currentRevenue - $lastRevenue) / $lastRevenue) * 100 : 100,

            'currentOrders' => $currentOrders,
            'orderChange' => $lastOrders > 0 ? (($currentOrders - $lastOrders) / $lastOrders) * 100 : 100,

            'totalProducts' => $totalProducts,
            'newProducts' => $newProducts,

            'totalCustomers' => $totalCustomers,
            'customerChange' => $lastCustomers > 0 ? (($totalCustomers - $lastCustomers) / $lastCustomers) * 100 : 100,

            'topProducts' => $topProducts,
            'recentOrders' => $recentOrders
        ]);
    }

}
