<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductDetail;
use Illuminate\Http\Request;

class DisplayProductController extends Controller
{
//    Hien thi san pham va danh muc o trang chu
    public function customerPage()
    {
        $products = Product::with(['firstImage', 'category'])
            ->where('isDeleted', 0)
            ->whereHas('category', function ($query) {
                $query->where('isDeleted', 0);
            })
            ->take(4)
            ->get();
        $categories = Category::where('isDeleted', false)->get();
        return view('UserPage.HomePage', compact('products', 'categories'));
    }
//    Hien thi san pham
    public function index()
    {
        $products = Product::with(['firstImage', 'category'])
            ->where('isDeleted', 0)
            ->whereHas('category', function ($query) {
                $query->where('isDeleted', 0);
            })
            ->paginate(8);
        $categories = Category::where('isDeleted', false)->get();
        return view('UserPage.Product', compact('products','categories'));
    }
//    Hien thi chi tiet san pham
    public function productDetails($productID)
    {
        // Lay nhieu hinh anh
        $product = Product::with('images')->where('productID', $productID)->firstOrFail();

        // Lay 1 hinh anh
        $products = Product::with(['firstImage', 'category'])
            ->where('isDeleted', 0)
            ->whereHas('category', function ($query) {
                $query->where('isDeleted', 0);
            })
            ->take(4)
            ->get();
        $productDetails = ProductDetail::with(['product', 'size', 'color'])
            ->where('isDeleted', false)
            ->where('prdID', $product->productID)
            ->get();

        return view('UserPage.ProductDetails', compact('product','products','productDetails'));
    }

}
