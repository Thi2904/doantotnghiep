<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Payment;
use App\Models\Product;
use App\Models\ProductDetail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrdersManageController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $query = Order::with(['customer', 'status', 'orderDetails.productDetail.product.firstImage'])
            ->orderBy('created_at', 'desc');

        $status = $request->input('statusID');

        if (!empty($status)) {
            $query->whereHas('status', function ($q) use ($status) {
                $q->where('statusID', $status);
            });
        }
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('orderID', 'like', '%' . $search . '%')
                    ->orWhereHas('customer', function ($q2) use ($search) {
                        $q2->where('phone', 'like', '%' . $search . '%');
                    });
            });
        }
        $orders = $query->paginate(10)->appends(['search' => $search]);
        $payments = Payment::all();
        $products = Product::with(['firstImage', 'category'])->where('isDeleted', 0)->get();
        $total = $query->count();
        $statusCounts = Order::selectRaw('staID, COUNT(*) as total')
            ->groupBy('staID')
            ->pluck('total', 'staID');

        // Tổng tất cả đơn hàng
        $totalOrders = Order::count();
        return view('AdminPage.Orders', compact('orders','total','payments','products','statusCounts', 'totalOrders'));
    }
    public function store(Request $request)
    {
        if ($request->phone){
            $length = strlen($request->phone);
            if ($length > 10) {
                return back()->with('error', 'Số điện thoại không được vượt quá 10 kí tự!');;
            }
        }
        $customer = User::where('phone',$request->phone)
            ->where('role','customer')
            ->where('isDeleted',0)
            ->first();
        if($customer != null){
            $order = new Order();
            $order->cusID = $customer->id; // hoặc từ $request
            $order->adminID = null; // hoặc từ $request
            $order->orderPhoneNumber = $request->phone;
            $order->shipping_street = null;
            $order->shipping_city = null;
            $order->shipping_district = null;
            $order->shipping_ward = null;
            $order->payID = $request->payID;
            $order->staID = 1;
            $order->totalPrice = $request->total ?? 0;
            $order->save();
        }else{
            $customer = new User();
            $customer->username = 'user_' . time(); // tạo username tạm thời, bạn có thể đổi theo ý
            $customer->name = $request->nameCus ?? 'Khách hàng'; // tên từ request hoặc mặc định
            $customer->email = 'user' . time() . '@example.com'; // email giả nếu không nhập
            $customer->phone = $request->phone;
            $customer->role = 'customer';
            $customer->password = bcrypt('123456'); // mật khẩu mặc định, có thể buộc đổi sau
            $customer->isDeleted = 0;
            $customer->save();

            // Tạo đơn hàng cho khách
            $order = new Order();
            $order->cusID = $customer->id;
            $order->adminID = null;
            $order->orderPhoneNumber = $request->phone;
            $order->shipping_street = null;
            $order->shipping_city = null;
            $order->shipping_district = null;
            $order->shipping_ward = null;
            $order->payID = $request->payID;
            $order->staID = 1;
            $order->totalPrice = $request->total ?? 0;
            $order->save();

        }

        return redirect()->route('order-manage.index')->with('success', 'Đơn hàng đã được tạo thành công bởi quản trị viên!');
    }
    public function approve(Request $request, $orderID)
    {
        $cusID = $request->input('cusID');
        $order = Order::where('orderID', $orderID)->where('cusID', $cusID)->first();

        $order->staID = 2;
        $order->save();

        return back()->with('success', "Đơn hàng của khách $cusID đã được duyệt.");
    }

    public function deliver(Request $request, $orderID)
    {
        $cusID = $request->input('cusID');
        $order = Order::where('orderID', $orderID)->where('cusID', $cusID)->first();

        $order->staID = 3;
        $order->shipping_code = $request->shipping_code;
        $order->save();

        return back()->with('success', "Đơn hàng của khách $cusID đã được gửi cho bên giao hàng.");
    }


    public function cancel(Request $request, $orderID)
    {
        $cusID = $request->input('cusID');
        $order = Order::where('orderID', $orderID)->where('cusID', $cusID)->first();

        $order->staID = 5;
        $order->save();

        return back()->with('success', "Đơn hàng của khách $cusID đã bị hủy.");
    }
    public function getSizes($productID)
    {
        $details = ProductDetail::where('prdID', $productID)
            ->where('isDeleted', false)
            ->with('size')
            ->get()
            ->unique('sizeId'); // Tránh lặp size

        $data = $details->map(function ($item) {
            return [
                'sizeId' => $item->sizeId,
                'size' => $item->size->sizeName,
            ];
        });

        return response()->json($data);
    }

    public function getColors($productID, $sizeId)
    {
        $details = ProductDetail::where('prdID', $productID)
            ->where('sizeId', $sizeId)
            ->where('isDeleted', false)
            ->with('color')
            ->get()
            ->unique('colorId'); // Tránh lặp màu

        $data = $details->map(function ($item) {
            return [
                'colorId' => $item->colorId,
                'color' => $item->color->colorName,
            ];
        });

        return response()->json($data);
    }
    public function showDetails($id)
    {

        $order = Order::with(['customer', 'admin', 'status', 'payment', 'orderDetails.productDetail.product.firstImage'])->findOrFail($id);
        $products = Product::where('isDeleted',0)
            ->whereHas('category', function ($q) {
                $q->where('isDeleted', 0);
            })->get();
        return view('AdminPage.OrderDetails', compact('order','products'));
    }


    public function addMoreDetails(Request $request, $id)
    {
        $products = $request->input('products');
        $grouped = array_chunk($products, 5); // nhóm mỗi sản phẩm gồm 5 item

        $totalPrice = 0;

        foreach ($grouped as $productGroup) {
            $productID = $productGroup[1]['prdID'] ?? null;
            $sizeID = $productGroup[2]['sizeId'] ?? null;
            $colorID = $productGroup[3]['colorId'] ?? null;
            $quantity = (int) ($productGroup[4]['quantity'] ?? 1);

            if (!$productID || !$sizeID || !$colorID) {
                continue;
            }

            $productDetail = ProductDetail::where('prdID', $productID)
                ->where('sizeId', $sizeID)
                ->where('colorId', $colorID)
                ->first();

            if (!$productDetail) {
                continue;
            }

            $product = Product::find($productID);
            if (!$product) {
                continue;
            }

            $unitPrice = $product->productSellPrice;
            $subtotal = $unitPrice * $quantity;
            $totalPrice += $subtotal;

            OrderDetail::create([
                'orderID' => $id,
                'productDetailID' => $productDetail->id,
                'orderQuantity' => $quantity,
                'unitPrice' => $unitPrice
            ]);
        }

        $order = Order::find($id);
        if ($order) {
            $order->staID = 4;
            $order->totalPrice = $totalPrice;
            $order->save();
        }
        $product = ProductDetail::find($productDetail->id);
        if ($product) {
            $product->productQuantity -= $quantity;
            if ($product->productQuantity < 0) {
                $product->productQuantity = 0;
            }
            $product->save();
        }
        if ($order->payID == 2) {
            return $this->redirectToVnpay($order->totalPrice, $order->orderID);
        }

        $order->isPayment = true;
        $order->save();
        return redirect()->back()->with('success', 'Đã thêm chi tiết đơn hàng và cập nhật trạng thái thành công.');
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
                return redirect()->route('orders.index')->with('success', 'Thanh toán thành công');

            } else {
                return redirect()->route('orders.index')->with('error', 'Thanh toán thất bại hoặc bị hủy');
            }
        }
    }
    public function filterOrders(Request $request)
    {
        $statusParam = $request->input('status', 'all'); // Mặc định là 'all'
        $searchQuery = $request->input('search');
        $perPage = 10; // Số lượng đơn hàng trên mỗi trang

        // Lấy tất cả các trạng thái từ database để ánh xạ
        $statuses = Status::pluck('staID', 'statusValue')->toArray();
        // Tạo một mảng ánh xạ ngược để tìm tên trạng thái từ staID
        $statusNames = array_flip($statuses);

        // Bắt đầu query
        $query = Order::with('status', 'orderDetails.productDetail.product.firstImage');

        // Áp dụng lọc theo trạng thái
        if ($statusParam !== 'all') {
            // Chuyển đổi tên trạng thái từ frontend sang staID tương ứng
            // Ví dụ: 'pending' -> 'Đang chờ duyệt' -> staID tương ứng
            $dbStatusValue = '';
            switch ($statusParam) {
                case 'pending':
                    $dbStatusValue = 'Đang chờ duyệt';
                    break;
                case 'approved':
                    $dbStatusValue = 'Đã duyệt';
                    break;
                case 'delivering':
                    $dbStatusValue = 'Đang giao hàng';
                    break;
                case 'delivered':
                    $dbStatusValue = 'Đã giao hàng';
                    break;
                case 'cancelled':
                    $dbStatusValue = 'Đã hủy';
                    break;
            }

            if (!empty($dbStatusValue) && isset($statuses[$dbStatusValue])) {
                $query->where('staID', $statuses[$dbStatusValue]);
            }
        }

        if ($searchQuery) {
            $query->where('orderID', 'like', '%' . $searchQuery . '%');
        }

        $orders = $query->orderByDesc('created_at')->paginate($perPage);

        $counts = [];
        $allOrdersCount = Order::count(); // Tổng số đơn hàng
        $counts['all'] = $allOrdersCount;

        foreach ($statuses as $statusName => $staID) {
            $countKey = $this->slugifyStatusName($statusName); // Chuyển đổi tên trạng thái sang slug
            $counts[$countKey] = Order::where('staID', $staID)->count();
        }

        return response()->json([
            'orders' => $orders,
            'counts' => $counts
        ]);
    }

    private function slugifyStatusName($statusName)
    {
        return str_replace('-', '', \Illuminate\Support\Str::slug($statusName));
    }


}
