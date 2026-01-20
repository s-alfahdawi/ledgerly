<x-guest-layout>
    <div class="container py-5">
        <div class="row justify-content-center align-items-center" style="min-height: 100vh;">
            <div class="col-md-8 col-lg-6 col-xl-5">
                <!-- Login Card -->
                <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
                    <!-- Header with Gradient -->
                    <div class="card-header bg-primary border-0 py-4" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                        <div class="text-center text-white">
                            <i class="mdi mdi-wallet" style="font-size: 3rem;"></i>
                            <h3 class="fw-bold mt-3 mb-1">Welcome Back</h3>
                            <p class="mb-0 opacity-75">Sign in to your account</p>
                        </div>
                    </div>
                    
                    <div class="card-body p-4 p-md-5">
                        <!-- Session Status -->
                        @if (session('status'))
                            <div class="alert alert-info mb-4" role="alert">
                                {{ session('status') }}
                            </div>
                        @endif

                        <form method="POST" action="{{ route('login') }}">
                            @csrf

                            <!-- Email Address -->
                            <div class="mb-4">
                                <label for="email" class="form-label fw-semibold">
                                    <i class="mdi mdi-email me-2 text-muted"></i>Email Address
                                </label>
                                <input 
                                    type="email" 
                                    class="form-control form-control-lg @error('email') is-invalid @enderror" 
                                    id="email" 
                                    name="email" 
                                    value="{{ old('email') }}" 
                                    required 
                                    autofocus 
                                    autocomplete="username"
                                    placeholder="Enter your email"
                                >
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Password -->
                            <div class="mb-4">
                                <label for="password" class="form-label fw-semibold">
                                    <i class="mdi mdi-lock me-2 text-muted"></i>Password
                                </label>
                                <input 
                                    type="password" 
                                    class="form-control form-control-lg @error('password') is-invalid @enderror" 
                                    id="password" 
                                    name="password" 
                                    required 
                                    autocomplete="current-password"
                                    placeholder="Enter your password"
                                >
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Remember Me & Forgot Password -->
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <div class="form-check">
                                    <input 
                                        class="form-check-input" 
                                        type="checkbox" 
                                        id="remember_me" 
                                        name="remember"
                                    >
                                    <label class="form-check-label text-muted" for="remember_me">
                                        Remember me
                                    </label>
                                </div>
                                @if (Route::has('password.request'))
                                    <a href="{{ route('password.request') }}" class="text-decoration-none text-primary fw-semibold">
                                        Forgot password?
                                    </a>
                                @endif
                            </div>

                            <!-- Submit Button -->
                            <button type="submit" class="btn btn-primary btn-lg w-100 mb-3 fw-semibold" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none;">
                                <i class="mdi mdi-login me-2"></i>Sign In
                            </button>
                        </form>

                        <!-- Register Link -->
                        <div class="text-center mt-4">
                            <p class="text-muted mb-0">
                                Don't have an account? 
                                <a href="{{ route('register') }}" class="text-primary fw-semibold text-decoration-none">Create one now</a>
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Back to Home -->
                <div class="text-center mt-4">
                    <a href="{{ route('landing') }}" class="text-decoration-none text-muted">
                        <i class="mdi mdi-arrow-left me-1"></i>Back to home
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-guest-layout>
