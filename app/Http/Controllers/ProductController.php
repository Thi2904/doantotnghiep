<?php

namespace App\Http\Controllers;

use App\Models\Color;
use App\Models\Product;
use App\Models\Category;
use App\Models\ProductDetail;
use App\Models\ProductImage;
use App\Models\Size;
use Illuminate\Http\Request;
use Illuminate\Support\Str;


class ProductController extends Controller
{
    public function index(Request $request)
    {


        $query = Product::with(['firstImage', 'category'])
            ->where('isDeleted', 0)
            ->whereHas('category', function ($q) {
                $q->where('isDeleted', 0);
            });

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('productName', 'like', "%$search%")
                    ->orWhere('productSellPrice', 'like', "%$search%");
            });
        }

        $products = $query->paginate(4)->appends(['search' => $search]);
        $total = $query->count();
        $categories = Category::where('isDeleted', false)->get();
        $sizes = Size::where('isDeleted', false)->get();
        $colors = Color::where('isDeleted', false)->get();

        return view('AdminPage.Products', compact('products', 'categories', 'search','sizes','colors','total'));
    }


    public function store(Request $request)
    {
        $request->validate([
            'productName' => 'required',
            'productBuyPrice' => 'required|numeric',
            'productSellPrice' => 'required|numeric',
            'productForGender' => 'required',
            'cateID' => 'required|exists:categories,categoryID',
            'productDesc' => 'required'

        ]);
        $category = Category::where('categoryID', $request->cateID)->first();

        $data = $request->all();

        if (Str::lower($category->categoryName) === 'áo thun') {
            $data['productCode'] = Product::generateProductCode('AT');
        }elseif (Str::lower($category->categoryName) === 'quần jeans'){
            $data['productCode'] = Product::generateProductCode('QJ');
        }elseif (Str::lower($category->categoryName) === 'quần đùi'){
            $data['productCode'] = Product::generateProductCode('QD');
        }

        Product::create($data);

        return redirect()->route('products.index')->with('success', 'Tạo sản phẩm thành công.');
    }

    public function update(Request $request, Product $product)
    {
        $request->validate([
            'productName' => 'required',
            'productBuyPrice' => 'required|numeric',
            'productSellPrice' => 'required|numeric',
            'productForGender' => 'required',
            'cateID' => 'required|exists:categories,categoryID',
            'productDesc' => 'required'

        ]);

        $category = Category::where('categoryID', $request->cateID)->first();

        $data = $request->only([
            'productName',
            'productBuyPrice',
            'productSellPrice',
            'productSize',
            'productColor',
            'productForGender',
            'productQuantity',
            'cateID'
        ]);

        if (Str::lower($category->categoryName) === 'áo thun' && empty($product->productCode)) {
            $data['productCode'] = Product::generateProductCode('AT');
        }

        $product->update($data);

        return redirect()->route('products.index')->with('success', 'Product updated successfully.');
    }

    public function destroy(Product $product)
    {
        $product->update(['isDeleted' => true]);
        return redirect()->route('products.index')->with('success', 'Product deleted (soft) successfully.');
    }
    public function getColorsBySize(Request $request)
    {
        $sizeName = $request->input('size');
        $prdID = $request->input('prdID');
        // Tìm sizeId từ sizeName
        $size = Size::where('sizeName', $sizeName)->first();

        if (!$size) {
            return response()->json(['colors' => []]);
        }

        $colors = ProductDetail::with('color')
            ->where('sizeId', $size->sizeId)
            ->where('prdID', $prdID)
            ->get()
            ->pluck('color.colorName')
            ->unique()
            ->values();

        return response()->json(['colors' => $colors]);
    }
    public function filterByCategory($cateID)
    {
        $products = Product::with(['firstImage', 'category'])
            ->where('isDeleted', 0)
            ->where('cateID', $cateID)
            ->paginate(8); // nếu bạn dùng phân trang

        $categories = Category::where('isDeleted', false)->get();

        return view('UserPage.Product', compact('products','categories'));
    }
}



