<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\CartDetail;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Payment;
use App\Models\Product;
use App\Models\ProductDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrdersController extends Controller
{
    public function index(Request $request)
    {
        $cartData = json_decode($request->input('cart_data'), true);

        $cartDetails = [];

        foreach ($cartData as $item) {
            $cartDetail = CartDetail::with(['product', 'product.firstImage','productDetail.size', 'productDetail.color'])
                ->find($item['id']);
            if ($cartDetail) {
                $cartDetail->quantity = $item['quantity']; // Cập nhật lại quantity nếu cần
                $cartDetails[] = $cartDetail;
            }
        }
        $subtotal = 0;

        foreach ($cartDetails as $detail) {
            $price = $detail->product->productSellPrice ?? 0;
            $quantity = $detail->quantity;
            $subtotal += $price * $quantity;
        }
        $shippingFee = 30000;

        $total = $subtotal + $shippingFee;

        $payments = Payment::all();

        return view('UserPage.Checkout', compact('cartDetails','total','subtotal','payments'));
    }

    public function storeFromCustomer(Request $request)
    {
        // Bước 1: Tạo đơn hàng
        $order = new Order();
        $order->cusID = auth()->id(); // hoặc từ $request
        $order->adminID = null; // hoặc từ $request
        $order->orderPhoneNumber = $request->phone;
        $order->shipping_street = $request->street_address;
        $order->shipping_city = $request->city_name;
        $order->shipping_district = $request->district_name;
        $order->shipping_ward = $request->ward_name;
        $order->payID = $request->payment;
        $order->staID = 1;
        $order->totalPrice = $request->total ?? 0; // nếu bạn có tính tổng
        $order->save(); // <<< ĐÂY LÀ DÒNG QUAN TRỌNG

        // Bước 2: Lưu chi tiết sản phẩm
        foreach ($request->productDetails as $index=>$productDetail) {

            OrderDetail::create([
                'orderID' => $order->orderID,
                'productDetailID' => $productDetail['productDetailID'],
                'orderQuantity' => $productDetail['quantity'],
                'unitPrice' => $productDetail['unitPrice'],
            ]);
        }

        $cartID = Cart::where('userID', auth()->id())->value('cartID');
        CartDetail::where('cartID', $cartID)->delete();


        return redirect()->route('customerPage')->with('success', 'Đơn hàng đã được tạo thành công!');
    }

    public function showOrders(Request $request)
    {
        $keyword = $request->input('keyword');

        $orders = Order::with([
            'status',
            'orderDetails.productDetail.product',
            'orderDetails.productDetail.product.images'
        ])
            ->whereHas('customer', function ($query) use ($keyword) {
                $query->where('name', 'like', "%$keyword%");
            })
            ->latest()
            ->get();

        $results = [];

        foreach ($orders as $order) {
            $productImages = [];
            foreach ($order->orderDetails as $detail) {
                $product = $detail->productDetail->product ?? null;
                $image = $product->images->first()->imageLink ?? null;

                if ($image) {
                    $productImages[] = $image;
                }
            }

            $results[] = [
                'orderID' => $order->orderID,
                'orderCode' => '#ORD-' . $order->created_at->format('Y-m-d') . '-' . $order->orderID,
                'orderDate' => $order->created_at->format('d/m/Y \l\ú\c H:i'),
                'expectedDelivery' => Carbon::parse($order->created_at)->addDays(3)->format('d/m/Y') . ' - ' . Carbon::parse($order->created_at)->addDays(5)->format('d/m/Y'),
                'status' => $order->status->statusValue ?? 'Không rõ',
                'statusClass' => $this->getStatusClass($order->status->statusValue ?? ''),
                'totalPrice' => number_format($order->totalPrice, 0, ',', '.') . 'đ',
                'productCount' => $order->orderDetails->sum('orderQuantity'),
                'productImages' => $productImages,
            ];
        }

        return view('UserPage.order-list', compact('results', 'keyword'));
    }
    private function getStatusClass($status)
    {
        return match ($status) {
            'Đang chờ duyệt'   => 'status-pending',
            'Đã duyệt'         => 'status-approved',
            'Đang giao hàng'   => 'status-shipping',
            'Đã giao'          => 'status-completed',
            'Đã hủy'           => 'status-cancelled',
            default            => 'status-default',
        };

    }
    public function showDetails($orderID)
    {
        $order = Order::with([
            'customer',
            'admin',
            'status',
            'payment',
            'orderDetails.productDetail.product',
            'orderDetails.productDetail.size',
            'orderDetails.productDetail.color',
        ])->findOrFail($orderID);

        return view('UserPage.orders-details', compact('order'));
    }



}
