<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Register - TounsiVert</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        body {
            background: linear-gradient(135deg, #2d6a4f 0%, #52b788 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            padding: 40px 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .register-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            overflow: hidden;
        }
        .register-header {
            background: linear-gradient(135deg, #2d6a4f 0%, #40916c 100%);
            color: white;
            padding: 40px;
            text-align: center;
        }
        .register-header i {
            font-size: 4rem;
            margin-bottom: 20px;
        }
        .register-body {
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
            <div class="col-md-6">
                <div class="register-card">
                    <div class="register-header">
                        <i class="bi bi-tree-fill"></i>
                        <h2 class="mb-0">Join TounsiVert</h2>
                        <p class="mb-0">Start making an impact today!</p>
                    </div>
                    <div class="register-body">
                        <form method="POST" action="{{ route('register') }}">
                            @csrf

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">First Name</label>
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <i class="bi bi-person"></i>
                                        </span>
                                        <input type="text" 
                                               class="form-control @error('first_name') is-invalid @enderror" 
                                               name="first_name" 
                                               value="{{ old('first_name') }}" 
                                               placeholder="John"
                                               required 
                                               autofocus>
                                    </div>
                                    @error('first_name')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">Last Name</label>
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <i class="bi bi-person"></i>
                                        </span>
                                        <input type="text" 
                                               class="form-control @error('last_name') is-invalid @enderror" 
                                               name="last_name" 
                                               value="{{ old('last_name') }}" 
                                               placeholder="Doe"
                                               required>
                                    </div>
                                    @error('last_name')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">Email Address</label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="bi bi-envelope"></i>
                                    </span>
                                    <input type="email" 
                                           class="form-control @error('email') is-invalid @enderror" 
                                           name="email" 
                                           value="{{ old('email') }}" 
                                           placeholder="john.doe@example.com"
                                           required>
                                </div>
                                @error('email')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">Region</label>
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <i class="bi bi-geo-alt"></i>
                                        </span>
                                        <input type="text" 
                                               class="form-control @error('region') is-invalid @enderror" 
                                               name="region" 
                                               value="{{ old('region') }}" 
                                               placeholder="Tunis"
                                               required>
                                    </div>
                                    @error('region')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">City</label>
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <i class="bi bi-geo"></i>
                                        </span>
                                        <input type="text" 
                                               class="form-control @error('city') is-invalid @enderror" 
                                               name="city" 
                                               value="{{ old('city') }}" 
                                               placeholder="Ariana"
                                               required>
                                    </div>
                                    @error('city')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">Password</label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="bi bi-lock"></i>
                                    </span>
                                    <input type="password" 
                                           class="form-control @error('password') is-invalid @enderror" 
                                           name="password" 
                                           placeholder="Min. 8 characters"
                                           required>
                                </div>
                                @error('password')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-bold">Confirm Password</label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="bi bi-lock-fill"></i>
                                    </span>
                                    <input type="password" 
                                           class="form-control" 
                                           name="password_confirmation" 
                                           placeholder="Repeat password"
                                           required>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary-custom w-100 mb-3">
                                <i class="bi bi-person-plus me-2"></i>Create Account
                            </button>

                            <div class="text-center">
                                <a href="{{ route('login') }}" class="text-decoration-none">
                                    Already have an account? <strong>Login here</strong>
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
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
