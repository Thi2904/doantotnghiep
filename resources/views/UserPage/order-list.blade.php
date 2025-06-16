<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đơn hàng của tôi - TrendyTeen</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="{{asset('css/customer/customerPage.css')}}">

    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        orange: {
                            50: '#fff7ed',
                            100: '#ffedd5',
                            200: '#fed7aa',
                            300: '#fdba74',
                            400: '#fb923c',
                            500: '#f97316',
                            600: '#ea580c',
                            700: '#c2410c',
                            800: '#9a3412',
                            900: '#7c2d12',
                        },
                        amber: {
                            50: '#fffbeb',
                            100: '#fef3c7',
                            200: '#fde68a',
                            300: '#fcd34d',
                            400: '#fbbf24',
                            500: '#f59e0b',
                            600: '#d97706',
                            700: '#b45309',
                            800: '#92400e',
                            900: '#78350f',
                        },
                    }
                }
            }
        }
    </script>

    <style>
        .order-card {
            transition: all 0.3s ease;
        }

        .order-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }

        .status-badge {
            font-size: 12px;
            font-weight: 600;
            padding: 4px 12px;
            border-radius: 20px;
        }

        .status-pending {
            background-color: #fef3c7;
            color: #d97706;
        }

        .status-confirmed {
            background-color: #dbeafe;
            color: #2563eb;
        }

        .status-processing {
            background-color: #e0e7ff;
            color: #7c3aed;
        }

        .status-shipping {
            background-color: #fef3c7;
            color: #f59e0b;
        }

        .status-delivered {
            background-color: #dcfce7;
            color: #16a34a;
        }

        .status-cancelled {
            background-color: #fee2e2;
            color: #dc2626;
        }

        .filter-tab {
            transition: all 0.3s ease;
        }

        .filter-tab.active {
            background-color: #f97316;
            color: white;
        }

        .search-box {
            position: relative;
        }

        .search-box input {
            padding-left: 40px;
        }

        .search-icon {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: #9ca3af;
        }
    </style>
</head>
<body class="min-h-screen bg-gradient-to-r from-amber-50 to-orange-50">
<!-- Header -->
<header class="sticky top-0 z-50 bg-white shadow-sm">
    <div class="container mx-auto px-4 py-3 flex justify-between items-center">
        <div class="flex items-center">
            <a href="{{route('customerPage')}}" class="text-2xl font-bold text-orange-500">Trendy<span class="text-gray-800">Teen</span></a>
        </div>

        <nav class="hidden md:flex space-x-8">
            <a href="#" class="nav-link text-gray-700 hover:text-orange-500 font-medium">Trang chủ</a>
            <a href="#" class="nav-link text-gray-700 hover:text-orange-500 font-medium">Nam</a>
            <a href="#" class="nav-link text-gray-700 hover:text-orange-500 font-medium">Nữ</a>
            <a href="#" class="nav-link text-gray-700 hover:text-orange-500 font-medium">Phụ kiện</a>
            <a href="#" class="nav-link text-gray-700 hover:text-orange-500 font-medium">Giới thiệu</a>
        </nav>

        <div class="flex items-center space-x-4">
            <div class="flex search">
                <div class="">
                    <form action="">
                        <div class="search-box">
                            <input placeholder="Search something" class="search_content" type="text">
                            <button style="display: none" type="submit"></button>
                        </div>
                    </form>
                </div>
                <div class="p-2 text-gray-600 hover:text-orange-500 relative">
                    <i class="search_icon fa-solid fa-magnifying-glass"></i>
                </div>
            </div>
            <button class="p-2 text-gray-600 hover:text-orange-500 relative">
                <a href="{{route('cart.index')}}">
                    <i class="fas fa-shopping-cart text-lg"></i>
                </a>
            </button>
            <button class="md:hidden p-2 text-gray-600">
                <i class="fas fa-bars text-xl"></i>
            </button>
            <div class="mr-3">
                @if(Auth::check())
                    <div class="flex items-center gap-1 cursor-pointer show_logout">
                        <div class="flex items-center space-x-2">
                            <span class="font-semibold text-blue-600">
                                {{ Auth::user()->username }}
                            </span>
                            <i id="dropDownTeacher" class="fa-solid fa-caret-down text-lg"></i>
                        </div>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">Đăng nhập</a>
                @endif

                <!-- Dropdown menu -->
                <div class="absolute right-0 mt-2 min-w-[160px] bg-white shadow-lg rounded-md border z-50 drop_down drop_downActive transition-all duration-200">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full text-left px-4 py-2 hover:bg-red-100 text-red-600 flex items-center gap-2">
                            <i class="fa-solid fa-arrow-right-from-bracket"></i>
                            Logout
                        </button>
                    </form>
                    <button class="w-full text-left px-4 py-2 hover:bg-gray-100 text-gray-700 flex items-center gap-2">
                        <a class="text-decoration: none; color: black" href="{{route('profile.edit')}}">
                            <i class="fa-solid fa-user"></i>
                            Profile
                        </a>
                    </button>
                </div>
            </div>




        </div>
    </div>
</header>

<!-- Main Content -->
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Page Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">Đơn hàng của tôi</h1>
        <p class="text-gray-600">Quản lý và theo dõi tất cả đơn hàng của bạn</p>
    </div>


    <!-- Orders List -->
    <div class="space-y-6">
        <!-- Order Card 1 -->
        @forelse ($results as $order)
            <div class="order-card bg-white rounded-lg shadow-sm border hover:shadow-lg mb-6">
                <div class="p-6">
                    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-4">
                        <div class="flex items-center gap-4 mb-2 md:mb-0">
                            <div>
                                <h3 class="font-semibold text-lg">{{ $order['orderCode'] }}</h3>
                                <p class="text-sm text-gray-500">Đặt ngày: {{ $order['orderDate'] }}</p>
                            </div>
                            <span class="status-badge {{ $order['statusClass'] }}">{{ $order['status'] }}</span>
                            @if($order['payStatus'])
                                <span class="status-badge status-delivered">Đã thanh toán</span>
                            @else
                                <span class="status-badge status-pending">Chưa thanh toán</span>
                            @endif
                        </div>
                        <div class="text-right">
                            <p class="text-lg font-bold text-orange-500">{{ $order['totalPrice'] }}</p>
                            <p class="text-sm text-gray-500">{{ $order['productCount'] }} sản phẩm</p>
                        </div>
                    </div>

                    <div class="flex gap-4 mb-4 overflow-x-auto">
                        @foreach ($order['productImages'] as $index => $image)
                            @if ($index < 3)
                                <div class="flex-shrink-0 w-16 h-16 bg-gray-100 rounded-lg overflow-hidden">
                                    <img src="{{ asset('storage/' . $image) }}" alt="Product" class="w-full h-full object-cover">
                                </div>
                            @endif
                        @endforeach
                        @if (count($order['productImages']) > 3)
                            <div class="flex-shrink-0 w-16 h-16 bg-gray-200 rounded-lg flex items-center justify-center">
                                <span class="text-gray-500 text-sm">+{{ count($order['productImages']) - 3 }}</span>
                            </div>
                        @endif
                    </div>

                    <div class="flex flex-col sm:flex-row gap-3 justify-between items-start sm:items-center pt-4 border-t">
                        <div class="text-sm text-gray-600">
                            <i class="fas fa-truck mr-2"></i>
                            Dự kiến giao: {{ $order['expectedDelivery'] }}
                        </div>
                        <div class="flex gap-2">

                            <button class=" text-orange-500 border border-orange-500 rounded-lg hover:bg-orange-50 text-sm font-medium">
                                <a style="display:block" class="px-4 py-2" href="/order-details{{$order['orderID']}}">
                                    Xem chi tiết
                                </a>
                            </button>
                            @if($order['status'] == 'Đang giao hàng')
                                <form action="{{route('orders.delivered', $order['orderID'])}}" method="POST">
                                    @csrf
                                    <button class="px-4 py-2 bg-orange-500 text-white rounded-lg hover:bg-orange-600 text-sm font-medium">
                                        Đã nhận hàng
                                    </button>
                                </form>
                            @elseif($order['status'] == 'Đang chờ duyệt' || $order['status'] == 'Đã duyệt')
                                <form method="POST" action="{{ route('orderCus.cancel', $order['orderID']) }}" onsubmit="return confirm('Bạn có chắc muốn hủy đơn này?')">
                                    @csrf
                                    <button class="px-4 py-2 bg-orange-500 text-white rounded-lg hover:bg-orange-600 text-sm font-medium">
                                        Hủy đơn hàng
                                    </button>
                                </form>
                            @elseif($order['status'] == 'Đã giao hàng')
                                <form>
                                @csrf
                                <button class="px-4 py-2 bg-orange-500 text-white rounded-lg hover:bg-orange-600 text-sm font-medium">
                                    Bình luận và đánh giá
                                </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <p>Không có đơn hàng nào cho "{{ $keyword }}"</p>
        @endforelse

    </div>

    <!-- Pagination -->
    <div class="flex justify-center items-center mt-12 space-x-2">
        <button class="px-3 py-2 text-gray-500 hover:text-orange-500 disabled:opacity-50" disabled>
            <i class="fas fa-chevron-left"></i>
        </button>
        <button class="px-4 py-2 bg-orange-500 text-white rounded-lg">1</button>
        <button class="px-4 py-2 text-gray-700 hover:bg-gray-100 rounded-lg">2</button>
        <button class="px-4 py-2 text-gray-700 hover:bg-gray-100 rounded-lg">3</button>
        <span class="px-2 text-gray-500">...</span>
        <button class="px-4 py-2 text-gray-700 hover:bg-gray-100 rounded-lg">10</button>
        <button class="px-3 py-2 text-gray-500 hover:text-orange-500">
            <i class="fas fa-chevron-right"></i>
        </button>
    </div>
</div>

<!-- Footer -->
<footer class="bg-gray-900 text-white pt-12 pb-6 mt-16">
    <div class="container mx-auto px-4">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 mb-8">
            <div>
                <h3 class="text-xl font-bold mb-4">TrendyTeen</h3>
                <p class="text-gray-400 mb-4">Thương hiệu thời trang trẻ trung, năng động dành cho giới trẻ Việt Nam.</p>
                <div class="flex space-x-4">
                    <a href="#" class="text-gray-400 hover:text-white"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" class="text-gray-400 hover:text-white"><i class="fab fa-instagram"></i></a>
                    <a href="#" class="text-gray-400 hover:text-white"><i class="fab fa-tiktok"></i></a>
                    <a href="#" class="text-gray-400 hover:text-white"><i class="fab fa-youtube"></i></a>
                </div>
            </div>

            <div>
                <h3 class="text-lg font-semibold mb-4">Mua sắm</h3>
                <ul class="space-y-2">
                    <li><a href="#" class="text-gray-400 hover:text-white">Sản phẩm mới</a></li>
                    <li><a href="#" class="text-gray-400 hover:text-white">Bán chạy nhất</a></li>
                    <li><a href="#" class="text-gray-400 hover:text-white">Khuyến mãi</a></li>
                    <li><a href="#" class="text-gray-400 hover:text-white">Bộ sưu tập</a></li>
                </ul>
            </div>

            <div>
                <h3 class="text-lg font-semibold mb-4">Thông tin</h3>
                <ul class="space-y-2">
                    <li><a href="#" class="text-gray-400 hover:text-white">Về chúng tôi</a></li>
                    <li><a href="#" class="text-gray-400 hover:text-white">Chính sách bảo mật</a></li>
                    <li><a href="#" class="text-gray-400 hover:text-white">Điều khoản dịch vụ</a></li>
                    <li><a href="#" class="text-gray-400 hover:text-white">Hướng dẫn mua hàng</a></li>
                </ul>
            </div>

            <div>
                <h3 class="text-lg font-semibold mb-4">Liên hệ</h3>
                <ul class="space-y-2 text-gray-400">
                    <li class="flex items-start">
                        <i class="fas fa-map-marker-alt mt-1 mr-3"></i>
                        <span>123 Đường ABC, Quận 1, TP.HCM</span>
                    </li>
                    <li class="flex items-center">
                        <i class="fas fa-phone-alt mr-3"></i>
                        <span>0909 123 456</span>
                    </li>
                    <li class="flex items-center">
                        <i class="fas fa-envelope mr-3"></i>
                        <span>support@trendyteen.com</span>
                    </li>
                </ul>
            </div>
        </div>

        <div class="border-t border-gray-800 pt-6 flex flex-col md:flex-row justify-between items-center">
            <p class="text-gray-400 text-sm mb-4 md:mb-0">© 2023 TrendyTeen. All rights reserved.</p>
            <div class="flex space-x-6">
                <img src="https://via.placeholder.com/40x25" alt="Payment method" class="h-6">
                <img src="https://via.placeholder.com/40x25" alt="Payment method" class="h-6">
                <img src="https://via.placeholder.com/40x25" alt="Payment method" class="h-6">
                <img src="https://via.placeholder.com/40x25" alt="Payment method" class="h-6">
            </div>
        </div>
    </div>
</footer>

<script src="{{asset('js/Customer/Sidebar.js')}}"></script>
<script src="{{asset('js/Customer/customerPage.js')}}"></script>
</body>
</html>
