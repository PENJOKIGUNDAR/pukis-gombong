<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'Pukis Gombong') }} - Login</title>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-container {
            max-width: 450px;
            width: 100%;
            padding: 0 15px;
        }
        .card {
            border: none;
            border-radius: 1rem;
            box-shadow: 0 1rem 3rem rgba(0, 0, 0, 0.15);
            overflow: hidden;
        }
        .card-header {
            background: linear-gradient(135deg, #0d6efd 0%, #0b5ed7 100%);
            padding: 2rem 1.5rem;
            border-bottom: none;
            text-align: center;
        }
        .card-header .logo {
            font-size: 2.5rem;
            color: white;
            margin-bottom: 0.5rem;
        }
        .card-header h3 {
            color: white;
            font-weight: 600;
            margin-bottom: 0;
        }
        .card-body {
            padding: 2rem;
        }
        .form-control {
            padding: 0.75rem 1rem;
            border-radius: 0.5rem;
            background-color: #f8f9fa;
            border: 1px solid #ced4da;
        }
        .form-control:focus {
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
            border-color: #86b7fe;
        }
        .form-label {
            font-weight: 500;
            margin-bottom: 0.5rem;
        }
        .btn-primary {
            padding: 0.75rem 1rem;
            border-radius: 0.5rem;
            font-weight: 500;
            background: linear-gradient(135deg, #0d6efd 0%, #0b5ed7 100%);
            border: none;
        }
        .btn-primary:hover {
            background: linear-gradient(135deg, #0b5ed7 0%, #0a58ca 100%);
        }
        .card-footer {
            background-color: #f8f9fa;
            border-top: 1px solid #eeeeee;
            padding: 1.5rem 2rem;
            text-align: center;
        }
        .alert {
            border-radius: 0.5rem;
            border: none;
            margin-bottom: 1.5rem;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="card">
            <div class="card-header text-center">
                <div class="logo">
                    <i class="fas fa-cookie"></i>
                </div>
                <h3>Pukis Gombong</h3>
                <p class="text-white mb-0">Sistem Manajemen Usaha</p>
            </div>
            <div class="card-body">
                @if($errors->any())
                    <div class="alert alert-danger" role="alert">
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <form method="POST" action="{{ route('login.submit') }}">
                    @csrf
                    <div class="mb-4">
                        <label for="email" class="form-label">Email</label>
                        <div class="input-group mb-2">
                            <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" required autofocus placeholder="email@example.com">
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="password" class="form-label">Password</label>
                        <div class="input-group mb-2">
                            <span class="input-group-text"><i class="fas fa-lock"></i></span>
                            <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" required placeholder="••••••••">
                        </div>
                    </div>

                    <div class="mb-4 form-check">
                        <input type="checkbox" class="form-check-input" id="remember" name="remember" {{ old('remember') ? 'checked' : '' }}>
                        <label class="form-check-label" for="remember">Ingat saya</label>
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="fas fa-sign-in-alt me-2"></i>Login
                        </button>
                    </div>
                </form>
            </div>
            <div class="card-footer">
                <p class="mb-0 text-muted">&copy; {{ date('Y') }} Pukis Gombong. All rights reserved.</p>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 