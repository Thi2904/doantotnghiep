<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fashion Admin - Quản Lý Cửa Hàng</title>
    <link rel="stylesheet" href="{{asset('css/admin/styles.css')}}">
    <link rel="stylesheet" href="{{asset('css/admin/stylePopup.css')}}">
    <link rel="stylesheet" href="{{asset('css/admin/additional-styles.css')}}">
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
            <a href="{{route('adminDashboard')}}" class="sidebar-link active" data-section="dashboard">
                <i class="fa-solid fa-chart-line"></i>
                <span>Tổng Quan</span>
            </a>
            <a href="{{route('categories.index')}}" class="sidebar-link" data-section="products">
                <i class="fa-regular fa-rectangle-list"></i>
                <span>Danh Mục Sản Phẩm</span>
            </a>
            <a href="{{route('products.index')}}" class="sidebar-link" data-section="products">
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
            <a href="{{route('order-manage.index')}}" class="sidebar-link" data-section="orders">
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
                    <h1 class="section-title">Chi Tiết Sản Phẩm</h1>
                    <p class="section-description">Quản lý thông tin chi tiết sản phẩm và biến thể</p>
                </div>
            </div>

            <!-- Product Information Card -->
            <div class="product-info-card">
                <div class="card-header">
                    <h2 class="card-title">Thông Tin Sản Phẩm</h2>
                </div>
                <div class="card-content">
                    <div class="product-detail-grid">
                        <!-- Product Images -->
                        <div class="product-images">
                            <div class="main-image">
                                <img src="{{ asset('storage/' . $infoProduct->images[0]->imageLink) }}" alt="Áo Thun Cotton Premium">
                            </div>
                            <button id="prev-btn" class="left-2">
                                <i class="fa-solid fa-arrow-left"></i>
                            </button>
                            <button id="next-btn" class="right-2">
                                <i class="fa-solid fa-arrow-right"></i>
                            </button>
                            <div class="thumbnail-grid">
                                @foreach($infoProduct->images as $index => $image)
                                    <img height="100px" width="100px" src="{{ asset('storage/' . $image->imageLink) }}" alt="Thumbnail 3" class="thumbnail">
                                @endforeach

                            </div>
                        </div>

                        <!-- Product Details -->
                        <div class="product-details">
                            <h2 class="product-title">{{$infoProduct -> productName}}</h2>
                            <p class="product-description">
                                {{$infoProduct -> productDesc}}
                            </p>

                            <div class="product-meta-grid">
                                <div class="meta-item">
                                    <label>Mã Sản Phẩm</label>
                                    <span> {{$infoProduct -> productCode}}</span>
                                </div>
                                <div class="meta-item">
                                    <label>Danh Mục</label>
                                    <span> {{$infoProduct -> category -> categoryName}}</span>
                                </div>
                                <div class="meta-item">
                                    <label>Giá Bán</label>
                                    <span> {{number_format($infoProduct->productSellPrice, 0, ',', '.')}}đ</span>
                                </div>
                                <div class="meta-item">
                                    <label>Trạng Thái</label>
                                    <span class="status-badge in-stock">Đang Bán</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <div class="table-container">
            <div class="table-wrapper">
                    <table class="data-table" id="products-table">
                        <thead>
                        <tr>
                            <th>Kích thước</th>
                            <th>Màu sắc</th>
                            <th>Số lượng</th>
                            <th>Thao tác</th>
                        </tr>
                        </thead>
                        <tbody id="products-tbody">

                        @forelse ($productDetails as $productDetail)
                            <div id="formDetailsOverlay-{{ $productDetail->id }}" class="form-overlay">
                                <div class="form-container"  style="top: -10%;">
                                    <span onclick="closeDetailsForm({{ $productDetail->id }})" class="close-btn">×</span>
                                    <form action="{{ route('product-details.update' , $productDetail->id) }}" method="POST" enctype="multipart/form-data">
                                        @csrf
                                        @method('PUT')
                                        <input type="number" id="prdID" name="prdID" value="{{$productDetail->prdID}}" hidden/>
                                        <div class="form-group">
                                            <label for="sizeId">Chọn kích thước sản phẩm</label>
                                            <select name="sizeId" id="sizeId" class="styled-select">
                                                @foreach($sizes as $size)
                                                    <option value="{{ $size->sizeId }}"
                                                        {{ $size->sizeId == $productDetail->sizeId ? 'selected' : '' }}>
                                                        {{ $size->sizeName }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label for="colorId">Chọn màu sản phẩm </label>
                                            <select name="colorId" id="colorId" class="styled-select">
                                                @foreach($colors as $color)
                                                    <option value="{{ $color->colorId }}"
                                                        {{ $color->colorId == $productDetail->colorId ? 'selected' : '' }}>
                                                        {{ $color->colorName }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label for="productQuantity">Số lượng sản phẩm</label>
                                            <input type="number" id="productQuantity" name="productQuantity" value="{{$productDetail->productQuantity}}" />
                                        </div>
                                        <button type="submit" class="submit-btn edit-submit-btn">Tạo</button>
                                    </form>
                                </div>
                            </div>
                            <tr>
                                <td>
                                    {{$productDetail->size->sizeName ?? 'N/A'}}
                                </td>
                                <td>{{$productDetail->color->colorName ?? 'N/A'}}</td>
                                <td>
                                    {{$productDetail->productQuantity ?? 'N/A'}}
                                </td>
                                <td style="text-align: left;">
                                    <button class="action-btn" onclick="showActionMenu(event)">
                                        <i class="fa-solid fa-ellipsis"></i>
                                    </button>
                                    <div class="dropdown-menu" id="action-dropdown">
                                        <button class="dropdown-item" onclick="openDetailsForm({{ $productDetail->id }})" style="border: none; background: white; width: 100% ;text-align: left">
                                            <span>Chỉnh sửa</span>
                                        </button>
                                        <hr>
                                        <form
                                            action="{{ route('product-details.destroy', $productDetail->id) }}"
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
                    <div class="page-info">Page {{ $productDetails->currentPage() }} of {{ $productDetails->lastPage() }}</div>
                    <div class="page-links">
                        @if($productDetails->currentPage() > 1)
                            <a href="{{ $productDetails->previousPageUrl() }}" class="custom-pagination-link">&laquo; Previous</a>
                        @endif

                        @for($i = 1; $i <= $productDetails->lastPage(); $i++)
                            @if($i == $productDetails->currentPage())
                                <span class="custom-pagination-link current-page">{{ $i }}</span>
                            @else
                                <a href="{{ $productDetails->url($i) }}" class="custom-pagination-link">{{ $i }}</a>
                            @endif
                        @endfor

                        @if($productDetails->hasMorePages())
                            <a href="{{ $productDetails->nextPageUrl() }}" class="custom-pagination-link">Next &raquo;</a>
                        @endif
                    </div>
                </div>
                <br>
            </div>
        </div>
    </section>
</div>

<script>
    const thumbnails = document.querySelectorAll('.thumbnail');
    const mainImage = document.querySelector('.main-image img');

    let currentIndex = 0;
    const productImages = Array.from(thumbnails).map(thumb => thumb.src.replace('w=100&h=100', 'w=400&h=400'));

    // Click thumbnail
    thumbnails.forEach((thumb, index) => {
        thumb.addEventListener('click', function () {
            currentIndex = index;
            updateMainImage(currentIndex);
        });
    });

    function updateMainImage(index) {
        mainImage.src = productImages[index];

        // Update active thumbnail
        thumbnails.forEach(t => t.classList.remove('active'));
        thumbnails[index].classList.add('active');
    }

    // Prev/Next
    const prevBtn = document.getElementById('prev-btn');
    const nextBtn = document.getElementById('next-btn');

    prevBtn.addEventListener('click', function () {
        currentIndex = (currentIndex - 1 + productImages.length) % productImages.length;
        updateMainImage(currentIndex);
    });

    nextBtn.addEventListener('click', function () {
        currentIndex = (currentIndex + 1) % productImages.length;
        updateMainImage(currentIndex);
    });

    // Khởi tạo ảnh đầu tiên là active
    updateMainImage(currentIndex);

</script>

<script src="{{asset('js/Admin/jsPopup.js')}}"></script>

<script src="{{asset('js/Admin/script.js')}}"></script>
</body>
</html>
