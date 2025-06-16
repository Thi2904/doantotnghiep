<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fashion Admin - Quản Lý Cửa Hàng</title>
    <link rel="stylesheet" href="{{asset('css/admin/styles.css')}}">
    <!-- Font Google -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Lucide Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet" />
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
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
<!-- Header -->
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

<!-- Main Layout -->
<div class="main-layout">
    <!-- Sidebar -->
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
            <a href="{{route('customer.index')}}" class="sidebar-link" data-section="customers">
                <i class="fa-regular fa-user"></i>
                <span>Khách Hàng</span>
            </a>
            <a href="{{route('discount_programs.index')}}" class="sidebar-link" data-section="settings">
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

    <!-- Main Content -->
    <main class="main-content">
        <!-- Dashboard Section -->
        <section id="dashboard-section" class="content-section active">
            <div class="section-header">
                <h1 class="section-title">Tổng Quan</h1>
                <p class="section-description">Xem tổng quan về hoạt động kinh doanh của cửa hàng.</p>
            </div>

            <!-- Stats Cards -->
            <div class="stats-grid">
                <!-- Doanh thu -->
                <div class="stat-card">
                    <div class="stat-header">
                        <span class="stat-title">Doanh Thu</span>
                        <i data-lucide="dollar-sign" class="stat-icon"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-value">{{ number_format($currentRevenue, 0, ',', '.') }}đ</div>
                        <div class="stat-change {{ $revenueChange >= 0 ? 'positive' : 'negative' }}">
                            <i data-lucide="{{ $revenueChange >= 0 ? 'arrow-up-right' : 'arrow-down-right' }}"></i>
                            <span>{{ round(abs($revenueChange), 1) }}% so với tháng trước</span>
                        </div>
                    </div>
                </div>

                <!-- Đơn hàng -->
                <div class="stat-card">
                    <div class="stat-header">
                        <span class="stat-title">Đơn Hàng</span>
                        <i data-lucide="credit-card" class="stat-icon"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-value">+{{ $currentOrders }}</div>
                        <div class="stat-change {{ $orderChange >= 0 ? 'positive' : 'negative' }}">
                            <i data-lucide="{{ $orderChange >= 0 ? 'arrow-up-right' : 'arrow-down-right' }}"></i>
                            <span>{{ round(abs($orderChange), 1) }}% so với tháng trước</span>
                        </div>
                    </div>
                </div>

                <!-- Sản phẩm -->
                <div class="stat-card">
                    <div class="stat-header">
                        <span class="stat-title">Sản Phẩm</span>
                        <i data-lucide="package" class="stat-icon"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-value">{{ $totalProducts }}</div>
                        <div class="stat-description">{{ $newProducts }} sản phẩm mới trong tháng này</div>
                    </div>
                </div>

                <!-- Khách hàng -->
                <div class="stat-card">
                    <div class="stat-header">
                        <span class="stat-title">Khách Hàng</span>
                        <i data-lucide="users" class="stat-icon"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-value">+{{ $totalCustomers }}</div>
                        <div class="stat-change {{ $customerChange >= 0 ? 'positive' : 'negative' }}">
                            <i data-lucide="{{ $customerChange >= 0 ? 'arrow-up-right' : 'arrow-down-right' }}"></i>
                            <span>{{ round(abs($customerChange), 1) }}% so với tháng trước</span>
                        </div>
                    </div>
                </div>
            </div>


            <!-- Dashboard Content Grid -->
            <div class="dashboard-grid">
                <!-- Top Products -->
                <div class="dashboard-card">
                    <div class="card-header">
                        <h3 class="card-title">Sản Phẩm Bán Chạy</h3>
                        <p class="card-description">Top 5 sản phẩm bán chạy nhất tháng này</p>
                    </div>
                    <div class="card-content">
                        <div class="product-list">
                            @foreach ($topProducts as $product)
                                <div class="product-item">
                                    <img src="{{ asset('storage/' . $product->firstImage->imageLink) }}" alt="Product" class="product-image">
                                    <div class="product-info">
                                        <p class="product-name">{{ $product->productName }}</p>
                                        <p class="product-meta">SKU: #{{ $product->productCode }} | Đã bán: {{ $product->total_sold }}</p>
                                    </div>
                                    <div class="product-price">{{ number_format($product->productSellPrice, 0, ',', '.') }}đ</div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>


                <!-- Recent Orders -->
                <div class="dashboard-card">
                    <div class="card-header">
                        <h3 class="card-title">Đơn Hàng Gần Đây</h3>
                        <p class="card-description">5 đơn hàng mới nhất trong hệ thống</p>
                    </div>
                    <div class="card-content">
                        <div class="order-list">
                            @foreach ($recentOrders as $order)
                                <div class="order-item">
                                    <div class="order-info">
                                        <p class="order-id">Đơn #ORD-{{ $order->orderID }}</p>
                                        <p class="order-customer">{{ $order->customer->name ?? 'N/A' }}</p>
                                    </div>
                                    <div class="order-details">
                        <span class="order-status {{ strtolower(str_replace(' ', '-', $order->status->statusName)) }}">
                            {{ $order->status->statusName }}
                        </span>
                                        <p class="order-amount">{{ number_format($order->totalPrice, 0, ',', '.') }}đ</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

            </div>
        </section>
    </main>
</div>

<script src="{{asset('js/Admin/script.js')}}"></script>
</body>
</html>
