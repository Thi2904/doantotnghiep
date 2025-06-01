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
                <div class="stat-card">
                    <div class="stat-header">
                        <span class="stat-title">Doanh Thu</span>
                        <i data-lucide="dollar-sign" class="stat-icon"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-value">120.350.000đ</div>
                        <div class="stat-change positive">
                            <i data-lucide="arrow-up-right"></i>
                            <span>20.5% so với tháng trước</span>
                        </div>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-header">
                        <span class="stat-title">Đơn Hàng</span>
                        <i data-lucide="credit-card" class="stat-icon"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-value">+573</div>
                        <div class="stat-change positive">
                            <i data-lucide="arrow-up-right"></i>
                            <span>12.2% so với tháng trước</span>
                        </div>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-header">
                        <span class="stat-title">Sản Phẩm</span>
                        <i data-lucide="package" class="stat-icon"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-value">248</div>
                        <div class="stat-description">24 sản phẩm mới trong tháng này</div>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-header">
                        <span class="stat-title">Khách Hàng</span>
                        <i data-lucide="users" class="stat-icon"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-value">+2,350</div>
                        <div class="stat-change positive">
                            <i data-lucide="arrow-up-right"></i>
                            <span>18.1% so với tháng trước</span>
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
                            <div class="product-item">
                                <img src="https://via.placeholder.com/48" alt="Product" class="product-image">
                                <div class="product-info">
                                    <p class="product-name">Áo Polo Nam Premium</p>
                                    <p class="product-meta">SKU: #AP-1234 | Đã bán: 245</p>
                                </div>
                                <div class="product-price">650.000đ</div>
                            </div>
                            <div class="product-item">
                                <img src="https://via.placeholder.com/48" alt="Product" class="product-image">
                                <div class="product-info">
                                    <p class="product-name">Áo Sơ Mi Linen Cao Cấp</p>
                                    <p class="product-meta">SKU: #AS-5678 | Đã bán: 198</p>
                                </div>
                                <div class="product-price">750.000đ</div>
                            </div>
                            <div class="product-item">
                                <img src="https://via.placeholder.com/48" alt="Product" class="product-image">
                                <div class="product-info">
                                    <p class="product-name">Quần Jeans Slim Fit</p>
                                    <p class="product-meta">SKU: #QJ-9012 | Đã bán: 187</p>
                                </div>
                                <div class="product-price">850.000đ</div>
                            </div>
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
                            <div class="order-item">
                                <div class="order-info">
                                    <p class="order-id">Đơn #ORD-5592</p>
                                    <p class="order-customer">Nguyễn Văn A</p>
                                </div>
                                <div class="order-details">
                                    <span class="order-status delivered">Đã giao</span>
                                    <p class="order-amount">1.250.000đ</p>
                                </div>
                            </div>
                            <div class="order-item">
                                <div class="order-info">
                                    <p class="order-id">Đơn #ORD-5591</p>
                                    <p class="order-customer">Trần Thị B</p>
                                </div>
                                <div class="order-details">
                                    <span class="order-status shipping">Đang giao</span>
                                    <p class="order-amount">850.000đ</p>
                                </div>
                            </div>
                            <div class="order-item">
                                <div class="order-info">
                                    <p class="order-id">Đơn #ORD-5590</p>
                                    <p class="order-customer">Lê Văn C</p>
                                </div>
                                <div class="order-details">
                                    <span class="order-status processing">Đang xử lý</span>
                                    <p class="order-amount">2.150.000đ</p>
                                </div>
                            </div>
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
