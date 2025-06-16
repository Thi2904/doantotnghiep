<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\CartDetail;
use App\Models\DiscountProgram;
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
        $discountCode = $request->input('discount_code');

        $cartDetails = [];

        foreach ($cartData as $item) {
            $cartDetail = CartDetail::with(['product', 'product.firstImage','productDetail.size', 'productDetail.color'])
                ->find($item['id']);
            if ($cartDetail) {
                $cartDetail->quantity = $item['quantity'];
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

        $discountValue = 0;

        if (!empty($discountCode)) {
            $program = DiscountProgram::find($discountCode);
            if ($program) {
                $discountValue = $program->discount_value;
            }
        }

        $total = $subtotal + $shippingFee - $discountValue;

        if ($total < 0) {
            $total = 0;
        }

        $payments = Payment::all();

        return view('UserPage.Checkout', compact(
            'cartDetails',
            'total',
            'subtotal',
            'shippingFee',
            'discountValue',
            'payments'
        ));
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
        $order->totalPrice = $request->total ?? 0;
        $order->save();

        // Bước 2: Lưu chi tiết sản phẩm
        foreach ($request->productDetails as $productDetail) {

            OrderDetail::create([
                'orderID' => $order->orderID,
                'productDetailID' => $productDetail['productDetailID'],
                'orderQuantity' => $productDetail['quantity'],
                'unitPrice' => $productDetail['unitPrice'],
            ]);
        }

        $product = ProductDetail::find($productDetail['productDetailID']);
        if ($product) {
            $product->productQuantity -= $productDetail['quantity'];
            if ($product->productQuantity < 0) {
                $product->productQuantity = 0;
            }
            $product->save();
        }
        $cartID = Cart::where('userID', auth()->id())->value('cartID');
        CartDetail::where('cartID', $cartID)->delete();


        if ($request->payment == 2) {
            return $this->redirectToVnpay($order->totalPrice, $order->orderID);
        }

        return redirect()->route('customerPage')->with('success', 'Đơn hàng đã được tạo thành công!');
    }
    public function redirectToVnpay($amount, $orderId)
    {
        date_default_timezone_set('Asia/Ho_Chi_Minh');
        $vnp_TmnCode = "NJJ0R8FS";
        $vnp_HashSecret = "BYKJBHPPZKQMKBIBGGXIYKWYFAYSJXCW";
        $vnp_Url = "https://sandbox.vnpayment.vn/paymentv2/vpcpay.html";
        $vnp_Returnurl = route('vnpay.return');


        $vnp_TxnRef = $orderId . "_" . time();
        $vnp_OrderInfo = "Thanh toán đơn hàng #$orderId";
        $vnp_OrderType = 'billpayment';
        $vnp_Amount = $amount * 100;
        $vnp_Locale = 'vn';
        $vnp_BankCode = 'NCB';
        $vnp_IpAddr = request()->ip();
        $vnp_ExpireDate = date('YmdHis', strtotime('+15 minutes'));

        $inputData = [
            "vnp_Version" => "2.1.0",
            "vnp_TmnCode" => $vnp_TmnCode,
            "vnp_Amount" => $vnp_Amount,
            "vnp_Command" => "pay",
            "vnp_CreateDate" => date('YmdHis'),
            "vnp_CurrCode" => "VND",
            "vnp_IpAddr" => $vnp_IpAddr,
            "vnp_Locale" => $vnp_Locale,
            "vnp_OrderInfo" => $vnp_OrderInfo,
            "vnp_OrderType" => $vnp_OrderType,
            "vnp_ReturnUrl" => $vnp_Returnurl,
            "vnp_TxnRef" => $vnp_TxnRef,
            "vnp_ExpireDate" => $vnp_ExpireDate,
            "vnp_BankCode" => $vnp_BankCode
        ];

        ksort($inputData);
        $query = "";
        $hashdata = "";
        foreach ($inputData as $key => $value) {
            $query .= urlencode($key) . "=" . urlencode($value) . "&";
            $hashdata .= $hashdata ? "&" : "";
            $hashdata .= urlencode($key) . "=" . urlencode($value);
        }

        $vnpSecureHash = hash_hmac('sha512', $hashdata, $vnp_HashSecret);
        $vnp_Url .= "?" . $query . "vnp_SecureHash=" . $vnpSecureHash;

        return redirect($vnp_Url);
    }
    public function vnpayReturn(Request $request)
    {
        if ($request->vnp_ResponseCode == '00') {
            $txnParts = explode('_', $request->vnp_TxnRef);
            $orderId = $txnParts[0] ?? null;

            if ($orderId) {
                $order = Order::find($orderId);
                if ($order) {
                    $order->isPayment = true;
                    $order->save();
                }
            }

        if ($request->vnp_ResponseCode == '00') {
            return redirect()->route('customerPage')->with('success', 'Thanh toán thành công');

        } else {
            return redirect()->route('customerPage')->with('error', 'Thanh toán thất bại hoặc bị hủy');
        }
    }}


    public function showOrders(Request $request)
    {
        $keyword = $request->input('keyword');

        $query = Order::with(['customer', 'status', 'orderDetails.productDetail.product.firstImage'])
            ->orderBy('created_at', 'desc');

        $status = $request->input('statusID');

        if (!empty($status)) {
            $query->whereHas('status', function ($q) use ($status) {
                $q->where('statusID', $status);
            });
        }
        $orders = Order::with([
            'status',
            'orderDetails.productDetail.product',
            'orderDetails.productDetail.product.images'
        ])
            ->where('cusID',auth()->id())
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
                'payStatus' => $order->isPayment,
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
            'Đã giao hàng'          => 'status-delivered',
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
    public function delivered($orderID)
    {
        $cusID = auth()->id();
        $order = Order::where('orderID', $orderID)->where('cusID', $cusID)->first();

        $order = Order::find($orderID);
        if ($order) {
            $order->isPayment = true;
            $order->save();
        }
        $order->staID = 4;
        $order->save();

        return redirect()->route('orders.showOrders')->with('success', "Xác nhận thành công. Cảm ơn quý khách.");
    }
    public function cancel($orderID)
    {
        $cusID = auth()->id();
        $order = Order::where('orderID', $orderID)->where('cusID', $cusID)->first();

        $order->staID = 5;
        $order->save();

        return back()->with('success', "Đơn hàng của quý khách đã hủy thành công.");
    }


}
