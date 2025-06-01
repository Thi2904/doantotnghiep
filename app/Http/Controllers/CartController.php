<?php

namespace App\Http\Controllers;

use App\Models\ProductDetail;
use Illuminate\Http\Request;
use App\Models\Cart;
use App\Models\CartDetail;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $cart = Cart::firstOrCreate(['userID' => $user->id]);

        $cartDetails = $cart->cartDetails()
            ->with([
                'product.firstImage',
                'productDetail.size',
                'productDetail.color',
            ])
            ->get();

        $subtotal = 0;

        foreach ($cartDetails as $detail) {
            $price = $detail->product->productSellPrice ?? 0;
            $quantity = $detail->quantity;
            $subtotal += $price * $quantity;
        }

        $shippingFee = 30000;
        $total = $subtotal + $shippingFee ;

        return view('UserPage.Cart', compact('cartDetails','total','shippingFee'));
    }


    public function addToCart(Request $request)
    {
        $request->validate([
            'productID' => 'required|exists:products,productID',
            'quantity' => 'required|integer|min:1',
        ]);

        $size = $request->size;
        $color = $request->color;
        $sizeID = \App\Models\Size::where('sizeName', $size)->value('sizeId');
        $colorID = \App\Models\Color::where('colorName', $color)->value('colorId');
        $productDetailId = ProductDetail::where('prdID',$request->productID)->where('sizeId',$sizeID)->where('colorId',$colorID)->value('id');
        $user = Auth::user();
        $cart = Cart::firstOrCreate(['userID' => $user->id]);

        $cartDetail = CartDetail::where('cartID', $cart->cartID)
            ->where('productID', $request->productID)
            ->where('productDetailID', $productDetailId)
            ->first();

        if ($cartDetail) {
            $cartDetail->quantity += $request->quantity;
            $cartDetail->save();
        } else {
            CartDetail::create([
                'cartID' => $cart->cartID,
                'productID' => $request->productID,
                'productDetailID' => $productDetailId,
                'quantity' => $request->quantity,
            ]);
        }

        return redirect()->route('cart.index')->with('success', 'Đã thêm vào giỏ hàng');
    }

    public function removeItem($id)
    {
        $item = CartDetail::findOrFail($id);
        $item->delete();

        return redirect()->back()->with('success', 'Đã xóa sản phẩm khỏi giỏ hàng');
    }



}
