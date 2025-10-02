<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - TounsiVert</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        body {
            background: linear-gradient(135deg, #2d6a4f 0%, #52b788 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .login-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            overflow: hidden;
        }
        .login-header {
            background: linear-gradient(135deg, #2d6a4f 0%, #40916c 100%);
            color: white;
            padding: 40px;
            text-align: center;
        }
        .login-header i {
            font-size: 4rem;
            margin-bottom: 20px;
        }
        .login-body {
            padding: 40px;
        }
        .btn-primary-custom {
            background: #2d6a4f;
            border: none;
            padding: 12px;
            font-weight: 600;
            transition: all 0.3s;
        }
        .btn-primary-custom:hover {
            background: #40916c;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(45, 106, 79, 0.3);
        }
        .form-control:focus {
            border-color: #2d6a4f;
            box-shadow: 0 0 0 0.2rem rgba(45, 106, 79, 0.25);
        }
        .input-group-text {
            background: #f8f9fa;
            border-right: none;
        }
        .form-control {
            border-left: none;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-5">
                <div class="login-card">
                    <div class="login-header">
                        <i class="bi bi-tree-fill"></i>
                        <h2 class="mb-0">TounsiVert</h2>
                        <p class="mb-0">Welcome Back!</p>
                    </div>
                    <div class="login-body">
                        @if (session('status'))
                            <div class="alert alert-success mb-4">
                                {{ session('status') }}
                            </div>
                        @endif

                        <form method="POST" action="{{ route('login') }}">
                            @csrf

                            <div class="mb-4">
                                <label class="form-label fw-bold">Email Address</label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="bi bi-envelope"></i>
                                    </span>
                                    <input type="email" 
                                           class="form-control @error('email') is-invalid @enderror" 
                                           name="email" 
                                           value="{{ old('email') }}" 
                                           placeholder="Enter your email"
                                           required 
                                           autofocus>
                                </div>
                                @error('email')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-bold">Password</label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="bi bi-lock"></i>
                                    </span>
                                    <input type="password" 
                                           class="form-control @error('password') is-invalid @enderror" 
                                           name="password" 
                                           placeholder="Enter your password"
                                           required>
                                </div>
                                @error('password')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-4 form-check">
                                <input type="checkbox" class="form-check-input" id="remember" name="remember">
                                <label class="form-check-label" for="remember">
                                    Remember me
                                </label>
                            </div>

                            <button type="submit" class="btn btn-primary-custom w-100 mb-3">
                                <i class="bi bi-box-arrow-in-right me-2"></i>Login
                            </button>

                            <div class="text-center">
                                <a href="{{ route('register') }}" class="text-decoration-none">
                                    Don't have an account? <strong>Register here</strong>
                                </a>
                            </div>

                            <hr class="my-4">

                            <div class="text-center">
                                <a href="{{ route('home') }}" class="btn btn-outline-secondary btn-sm">
                                    <i class="bi bi-house me-1"></i>Back to Home
                                </a>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Demo Accounts Info -->
                <div class="card mt-3 bg-light">
                    <div class="card-body small">
                        <strong>Demo Accounts:</strong><br>
                        Admin: admin@tounsivert.tn / password<br>
                        Organizer: organizer@tounsivert.tn / password<br>
                        Member: member@tounsivert.tn / password
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
