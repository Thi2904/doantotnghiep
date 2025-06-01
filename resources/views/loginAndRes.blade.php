<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Login</title>
    <link rel="stylesheet" href="{{asset('css/login.css')}}">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet" />
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <style>
        #message-box {
            display: flex;
            align-items: center;
            font-size: 14px;
            margin: 5px 0 15px 0;
            color: #e74c3c; /* Màu đỏ mặc định khi có lỗi */
            gap: 6px;
        }

        #message-box.valid {
            color: #2ecc71; /* Màu xanh khi đúng */
        }

        #message-icon {
            font-size: 18px;
        }
    </style>
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
    <div class="container">
        <div class="form-box login">
            <form action="/login" method="POST">
                @csrf
                <h1>Login</h1>
                <div class="input-box">
                    <input name="username" type="text" placeholder="Username" required>
                    <i class='bx bxs-user'></i>
                </div>
                <div class="input-box">
                    <input name="password" type="password" placeholder="Password" required>
                    <i class='bx bxs-lock-alt' ></i>
                </div>
                <div class="forgot-link">
                    <a href="">Forgot Password</a>
                </div>
                <button type="submit" class="btn">Login</button>
                <p>or login with social platforms</p>
                <div class="social-icons">
                    <a href=""><i class='bx bxl-google' ></i></a>
                    <a href=""><i class='bx bxl-facebook' ></i></a>
                    <a href=""><i class='bx bxl-github' ></i></a>
                    <a href=""><i class='bx bxl-linkedin' ></i></a>
                </div>
            </form>
        </div>
        <div class="form-box register">
            <form id="registerForm" action="{{route('register')}}" method="POST" onsubmit="return validatePassword()">
                @csrf
                <h1>Registration</h1>
                <div class="input-box">
                    <input name="username" type="text" placeholder="Username" required >
                    <i class='bx bxs-user'></i>
                </div>
                <div class="input-box">
                    <input name="email" type="email" placeholder="Email" required>
                    <i class='bx bxl-gmail'></i>
                </div>
                <div class="input-box">
                    <input name="password" type="password" id="password" placeholder="Password" required>
                    <i class='bx bxs-lock-alt'></i>
                </div>
                <div class="input-box">
                    <input type="password" name="password_confirmation" id="confirmPassword" placeholder="Confirm password" required>
                    <i class='bx bxs-lock-alt'></i>
                </div>

                <!-- Thêm dòng này để hiển thị thông báo -->
                <div id="message-box">
                    <i class='bx bx-error-circle' id="message-icon"></i>
                    <span id="message-text"></span>
                </div>

                <button type="submit" class="btn">Register</button>
                <p>or register with social platforms</p>
                <div class="social-icons">
                    <a href=""><i class='bx bxl-google'></i></a>
                    <a href=""><i class='bx bxl-facebook'></i></a>
                    <a href=""><i class='bx bxl-github'></i></a>
                    <a href=""><i class='bx bxl-linkedin'></i></a>
                </div>
            </form>
        </div>


        <div class="toggle-box">
            <div class="toggle-panel toggle-left">
                <h1>Hello, Welcome</h1>
                <p>Don't have an account</p>
                <button class="btn register-btn">Register</button>
            </div>
            <div class="toggle-panel toggle-right">
                <h1>Welcome Back</h1>
                <p>Already have an account</p>
                <button class="btn login-btn">Login</button>
            </div>
        </div>
    </div>
    <script src="{{asset('js/login.js')}}"></script>
    <script>
        function isStrongPassword(password) {
            const minLength = 8;
            const hasUpperCase = /[A-Z]/.test(password);
            const hasSpecialChar = /[!@#$%^&*(),.?":{}|<>]/.test(password);
            return password.length >= minLength && hasUpperCase && hasSpecialChar;
        }

        function validatePassword() {
            const password = document.getElementById("password").value;
            const confirmPassword = document.getElementById("confirmPassword").value;
            const messageBox = document.getElementById("message-box");
            const messageText = document.getElementById("message-text");
            const messageIcon = document.getElementById("message-icon");

            if (password.length < 8) {
                messageBox.classList.remove("valid");
                messageText.textContent = "Mật khẩu cần tối thiểu 8 ký tự!";
                messageIcon.className = "bx bx-error-circle";
                return false;
            }

            if (!/[A-Z]/.test(password)) {
                messageBox.classList.remove("valid");
                messageText.textContent = "Mật khẩu phải có ít nhất 1 chữ viết hoa!";
                messageIcon.className = "bx bx-error-circle";
                return false;
            }

            if (!/[!@#$%^&*(),.?":{}|<>]/.test(password)) {
                messageBox.classList.remove("valid");
                messageText.textContent = "Mật khẩu phải có ít nhất 1 ký tự đặc biệt!";
                messageIcon.className = "bx bx-error-circle";
                return false;
            }

            if (password !== confirmPassword) {
                messageBox.classList.remove("valid");
                messageText.textContent = "Mật khẩu không khớp!";
                messageIcon.className = "bx bx-error-circle";
                return false;
            }

            messageBox.classList.add("valid");
            messageText.textContent = "Mật khẩu hợp lệ.";
            messageIcon.className = "bx bx-check-circle";
            return true;
        }

        document.getElementById("confirmPassword").addEventListener("input", validatePassword);
        document.getElementById("password").addEventListener("input", validatePassword);
    </script>

</body>
</html>
