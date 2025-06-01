<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fashion Admin - Quản Lý Cửa Hàng</title>
    <link rel="stylesheet" href="{{asset('css/admin/styles.css')}}">
    <link rel="stylesheet" href="{{asset('css/admin/stylePopup.css')}}">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet" />
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <title>CLOTHES | BKACAD</title>
</head>

<body>
@if(session()->has('success'))
    <script>
        toastr.options = {
            "progressBar": true,
            "closeButton": true
        }
        toastr.success("{{ session()->get('success') }}","Thành công!", {timeOut:5000});
    </script>
@endif
@if(session()->has('error'))
    <script>
        toastr.options = {
            "progressBar": true,
            "closeButton": true
        }
        toastr.error("{{ session()->get('error')}}","Lỗi!", {timeOut:5000});
    </script>
@endif
@if(session()->has('warn'))
    <script>
        toastr.options = {
            "progressBar": true,
            "closeButton": true
        }
        toastr.warning("{{ session()->get('warn')}}","Thông báo!", {timeOut:5000});
    </script>
@endif
<header class="header">
    <div class="header-container">
        <div class="header-left">
            <div class="logo">
                <span class="logo-text">Fashion Admin</span>
            </div>
        </div>
        <div class="header-right">
            <div class="user-menu">
                <div class="user-info">
                    <p class="user-name">Admin</p>
                </div>
            </div>
        </div>
    </div>
</header>
<div class="main-layout">
    <aside class="sidebar">
        <nav class="sidebar-nav">
            <a href="{{route('adminDashboard')}}" class="sidebar-link " data-section="dashboard">
                <i class="fa-solid fa-chart-line"></i>
                <span>Tổng Quan</span>
            </a>
            <a href="{{route('categories.index')}}" class="sidebar-link" data-section="products">
                <i class="fa-regular fa-rectangle-list"></i>
                <span>Danh Mục Sản Phẩm</span>
            </a>
            <a href="{{route('products.index')}}" class="sidebar-link " data-section="products">
                <i class="fa-solid fa-box"></i>
                <span>Sản Phẩm</span>
            </a>
            <a href="#orders" class="sidebar-link" data-section="orders">
                <i class="fa-solid fa-palette"></i>
                <span>Màu Sản Phẩm</span>
            </a>
            <a href="{{route('customer.index')}}" class="sidebar-link" data-section="customers">
                <i class="fa-regular fa-user"></i>
                <span>Khách Hàng</span>
            </a>
            <a href="#settings" class="sidebar-link" data-section="settings">
                <i class="fa-solid fa-tags"></i>
                <span>Chương Trình Giảm Giá</span>
            </a>
            <a href="{{route('order-manage.index')}}" class="sidebar-link active" data-section="orders">
                <i class="fa-solid fa-receipt"></i>
                <span>Đơn Hàng</span>
            </a>
            <form method="post" action="{{route('logout')}}">
                @csrf
                <button style="border: none; background: white; margin-top: 100px" class="sidebar-link">
                    <i class="fa-solid fa-right-from-bracket"></i>
                    <span>Đăng Xuất</span>
                </button>
            </form>
        </nav>
    </aside>
    <div class="main-content">
        <section id="products-section" class="content-section active">
            <div class="section-header">
                <div>
                    <h1 class="section-title">Đơn Hàng</h1>
                    <b class="section-description">Tổng số lượng đơn hàng của cửa hàng: {{$total}}</b>
                </div>
                <button onclick="openForm()" class="btn btn-primary">
                    <i class="fa-solid fa-plus"></i>
                    Tạo đơn hàng mới
                </button>
                <div id="formOverlay" >
                    <div class="form-container" style="top: -10%;">
                        <span onclick="closeForm()" class="close-btn">×</span>
                        <form  id="productForm" action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="form-group">
                                <label for="productName">Khách hàng:</label>
                                <select name="cusID" required>
                                    @foreach ($customers as $customer)
                                        <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="phone">Số điện thoại</label>
                                <input type="number" id="phone" name="phone" placeholder="Số điện thoại" required>
                            </div>
                            <div class="form-group">
                                <label for="productBuyPrice">Giá nhập</label>
                                <input type="number" id="productBuyPrice" name="productBuyPrice" placeholder="Giá mua" required>
                            </div>
                            <div class="form-group">
                                <label for="productForGender">Chọn giới tính</label>
                                <select name="productForGender" id="productForGender" class="styled-select">
                                    <option value="0">Nam</option>
                                    <option value="1">Nữ</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="cateID">Chọn danh mục sản phẩm</label>
                                <select name="cateID" id="cateID" class="styled-select">
{{--                                    @foreach($categories as $category)--}}
{{--                                        <option value="{{ $category->categoryID }}">{{ $category->categoryName }}</option>--}}
{{--                                    @endforeach--}}
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="productDesc">Mô tả sản phẩm</label>
                                <textarea id="productDesc" name="productDesc" placeholder="Description"></textarea>
                            </div>

                            <button type="submit" class="submit-btn">Tạo</button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="table-container">
                <div class="table-header">
                    <div class="search-box">
                        <form method="GET" action="{{ route('order-manage.index') }}" style="margin-bottom: 16px;">
                            <i class="fa-solid fa-magnifying-glass"></i>
                            <input type="text" name="search" value="{{ request('search') }}" placeholder="Tìm kiếm sản phẩm..." id="product-search" />
                            <button type="submit" style="display: none"></button>
                        </form>
                    </div>
                </div>
                <div class="table-wrapper">
                    <table class="data-table" id="products-table">
                        <thead>
                        <tr>
                            <th>Tên người mua</th>
                            <th>Số điện thoại</th>
                            <th>Tổng giá trị đơn hàng</th>
                            <th>Trạng thái đơn hàng</th>
                            <th>Thao tác</th>
                        </tr>
                        </thead>
                        <tbody id="products-tbody">

                        @forelse ($orders as $order)
                            @php
                                $status = $order->status->statusValue ?? 'Không rõ';
                                $colorClass = '';

                                switch ($status) {
                                    case 'Đang chờ duyệt':
                                        $colorClass = 'bg-yellow-100 text-yellow-800';
                                        break;
                                    case 'Đã duyệt':
                                        $colorClass = 'bg-blue-100 text-blue-800';
                                        break;
                                    case 'Đang giao hàng':
                                        $colorClass = 'bg-purple-100 text-purple-800';
                                        break;
                                    case 'Đã giao hàng':
                                        $colorClass = 'bg-green-100 text-green-800';
                                        break;
                                    case 'Đã hủy':
                                        $colorClass = 'bg-red-100 text-red-800';
                                        break;
                                    default:
                                        $colorClass = 'bg-gray-100 text-gray-800';
                                }
                            @endphp
{{--                            <div id="editFormOverlay-{{ $product->productID }}" class="form-overlay">--}}
{{--                                <div class="form-container"  style="top: -10%;">--}}
{{--                                    <span onclick="closeEditForm({{ $product->productID }})" class="close-btn">×</span>--}}
{{--                                    <form action="{{ route('products.update', $product->productID) }}" method="POST" enctype="multipart/form-data">--}}
{{--                                        @csrf--}}
{{--                                        @method('PUT')--}}
{{--                                        <div class="form-group">--}}
{{--                                            <label for="editProductName">Tên sản phẩm</label>--}}
{{--                                            <input type="text" id="editProductName" name="productName" value="{{ $product->productName }}" required>--}}
{{--                                        </div>--}}
{{--                                        <div class="form-group">--}}
{{--                                            <label for="editProductSellPrice">Giá bán</label>--}}
{{--                                            <input type="number" id="editProductSellPrice" name="productSellPrice" value="{{ $product->productSellPrice }}" />--}}
{{--                                        </div>--}}
{{--                                        <div class="form-group">--}}
{{--                                            <label for="editProductBuyPrice">Giá nhập</label>--}}
{{--                                            <input type="number" id="editProductBuyPrice" name="productBuyPrice" value="{{ $product->productBuyPrice }}" />--}}
{{--                                        </div>--}}
{{--                                        <div class="form-group">--}}
{{--                                            <label for="editProductForGender">Chọn giới tính</label>--}}
{{--                                            <select name="productForGender" id="editProductForGender" class="styled-select">--}}
{{--                                                <option value="0" {{ $product->productForGender == 0 ? 'selected' : '' }}>Nam</option>--}}
{{--                                                <option value="1" {{ $product->productForGender == 1 ? 'selected' : '' }}>Nữ</option>--}}
{{--                                            </select>--}}
{{--                                        </div>--}}
{{--                                        <div class="form-group">--}}
{{--                                            <label for="editCateID">Chọn danh mục sản phẩm</label>--}}
{{--                                            <select name="cateID" id="editCateID" class="styled-select">--}}
{{--                                                @foreach($categories as $category)--}}
{{--                                                    <option value="{{ $category->categoryID }}"--}}
{{--                                                        {{ $category->categoryID == $product->cateID ? 'selected' : '' }}>--}}
{{--                                                        {{ $category->categoryName }}--}}
{{--                                                    </option>--}}
{{--                                                @endforeach--}}
{{--                                            </select>--}}
{{--                                        </div>--}}
{{--                                        <div class="form-group">--}}
{{--                                            <label for="productDesc">Mô tả sản phẩm</label>--}}
{{--                                            <textarea id="productDesc" name="productDesc" placeholder="Description">{{$product->productDesc}}</textarea>--}}
{{--                                        </div>--}}

{{--                                        <button type="submit" class="submit-btn edit-submit-btn">Cập nhật</button>--}}
{{--                                    </form>--}}
{{--                                </div>--}}
{{--                            </div>--}}

                            <tr>
                                <td>
                                    <div class="cell-product">
                                        <h4 style="max-width: 200px"  >
                                            <a style="text-decoration: none; color: black" onmouseover="this.style.color='blue';"  onmouseout="this.style.color='black';"  href="">
                                                {{$order->customer->name}}
                                            </a>
                                        </h4>
                                    </div>
                                </td>
                                <td>{{$order->orderPhoneNumber}}</td>
                                <td>{{number_format($order->totalPrice, 0, ',', '.')}}đ</td>
                                <td>
                                    <span class="status-badge status-{{ Str::slug($status) }}">{{ $status }}</span>
                                </td>

                                <td style="text-align: left;">
                                    <button class="action-btn" onclick="showActionMenu(event)">
                                        <i class="fa-solid fa-ellipsis"></i>
                                    </button>
                                    <div class="dropdown-menu" id="action-dropdown">
                                        <button class="dropdown-item" onclick="openEditForm({{ $order->orderID }})" style="border: none; background: white; width: 100% ;text-align: left">
                                            <span>Chỉnh sửa</span>
                                        </button>
                                        <hr>
                                        <form
{{--                                            action="{{ route('products.destroy', $product->productID) }}"--}}
                                            method="POST"
                                            onsubmit="return confirm('Bạn có chắc chắn muốn xóa sản phẩm này không?');"
                                            class="delete-form">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="dropdown-item"  style="border: none; background: white; width: 100% ;text-align: left" title="Delete product">
                                                <span style="color: red">Xóa</span>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>

                        @empty
                            <tr>
                                <td colspan="5" style="text-align: center;">Không có sản phẩm nào.</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                    <div style="padding: 0 10px" class="custom-pagination">
                        <div class="page-info">Page {{ $orders->currentPage() }} of {{ $orders->lastPage() }}</div>
                        <div class="page-links">
                            @if($orders->currentPage() > 1)
                                <a href="{{ $orders->previousPageUrl() }}" class="custom-pagination-link">&laquo; Previous</a>
                            @endif

                            @for($i = 1; $i <= $orders->lastPage(); $i++)
                                @if($i == $orders->currentPage())
                                    <span class="custom-pagination-link current-page">{{ $i }}</span>
                                @else
                                    <a href="{{ $orders->url($i) }}" class="custom-pagination-link">{{ $i }}</a>
                                @endif
                            @endfor

                            @if($orders->hasMorePages())
                                <a href="{{ $orders->nextPageUrl() }}" class="custom-pagination-link">Next &raquo;</a>
                            @endif
                        </div>
                    </div>
                    <br>
                </div>
            </div>
        </section>
    </div>
</div>

<script src="{{asset('js/Admin/jsPopup.js')}}"></script>
<script src="{{asset('js/Admin/script.js')}}"></script>
</body>
</html>
