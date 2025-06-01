<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderDetail;
use Illuminate\Http\Request;

class OrdersManageController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $query = Order::with(['customer','status'])
        ->whereHas('customer', function ($q) {
            $q->where('isDeleted', 0);
        });

        $orders = $query->paginate(4)->appends(['search' => $search]);
        $total = $query->count();

        return view('AdminPage.Orders', compact('orders','total'));
    }
    public function storeFromAdmin(Request $request)
    {
        $request->validate([
            'cusID' => 'required|exists:customers,id',
            'phone' => 'required',
            'street_address' => 'required',
            'city_name' => 'required',
            'district_name' => 'required',
            'ward_name' => 'required',
            'payment' => 'required|exists:payments,payID',
            'productDetails' => 'required|array',
        ]);

        $order = new Order();
        $order->cusID = $request->cusID;
        $order->adminID = auth()->id(); // ID của quản trị viên đang đăng nhập
        $order->orderPhoneNumber = $request->phone;
        $order->shipping_street = $request->street_address;
        $order->shipping_city = $request->city_name;
        $order->shipping_district = $request->district_name;
        $order->shipping_ward = $request->ward_name;
        $order->payID = $request->payment;
        $order->staID = 1;
        $order->totalPrice = $request->total ?? 0;
        $order->save();

        // Bước 2: Thêm chi tiết đơn hàng
        foreach ($request->productDetails as $productDetail) {

            OrderDetail::create([
                'orderID' => $order->orderID,
                'productDetailID' => $productDetail['productDetailID'],
                'orderQuantity' => $productDetail['quantity'],
                'unitPrice' => $productDetail['unitPrice'],
            ]);
        }

        return redirect()->route('admin.orders.index')->with('success', 'Đơn hàng đã được tạo thành công bởi quản trị viên!');
    }

}
