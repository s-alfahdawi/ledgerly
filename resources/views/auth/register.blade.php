<x-guest-layout>
    <div class="container py-5">
        <div class="row justify-content-center align-items-center" style="min-height: 100vh;">
            <div class="col-md-8 col-lg-6 col-xl-5">
                <!-- Register Card -->
                <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
                    <!-- Header with Gradient -->
                    <div class="card-header bg-primary border-0 py-4" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                        <div class="text-center text-white">
                            <i class="mdi mdi-account-plus" style="font-size: 3rem;"></i>
                            <h3 class="fw-bold mt-3 mb-1">Create Account</h3>
                            <p class="mb-0 opacity-75">Start managing your finances today</p>
                        </div>
                    </div>
                    
                    <div class="card-body p-4 p-md-5">
                        @if($invitation ?? null)
                            <div class="alert alert-info mb-4">
                                <i class="mdi mdi-information me-2"></i>
                                You've been invited to join <strong>{{ $invitation->account->name }}</strong> as a <strong>{{ ucfirst($invitation->role) }}</strong>.
                            </div>
                        @endif
                        <form method="POST" action="{{ route('register') }}">
                            @csrf
                            @if($invitation ?? null)
                                <input type="hidden" name="invitation_token" value="{{ $invitation->token }}">
                            @endif

                            <!-- Name -->
                            <div class="mb-3">
                                <label for="name" class="form-label fw-semibold">
                                    <i class="mdi mdi-account me-2 text-muted"></i>Full Name
                                </label>
                                <input 
                                    type="text" 
                                    class="form-control form-control-lg @error('name') is-invalid @enderror" 
                                    id="name" 
                                    name="name" 
                                    value="{{ old('name') }}" 
                                    required 
                                    autofocus 
                                    autocomplete="name"
                                    placeholder="Enter your full name"
                                >
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Email Address -->
                            <div class="mb-3">
                                <label for="email" class="form-label fw-semibold">
                                    <i class="mdi mdi-email me-2 text-muted"></i>Email Address
                                </label>
                                <input 
                                    type="email" 
                                    class="form-control form-control-lg @error('email') is-invalid @enderror" 
                                    id="email" 
                                    name="email" 
                                    value="{{ old('email', $invitation->email ?? '') }}" 
                                    required 
                                    autocomplete="username"
                                    placeholder="Enter your email"
                                    {{ ($invitation ?? null) ? 'readonly' : '' }}
                                >
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Password -->
                            <div class="mb-3">
                                <label for="password" class="form-label fw-semibold">
                                    <i class="mdi mdi-lock me-2 text-muted"></i>Password
                                </label>
                                <input 
                                    type="password" 
                                    class="form-control form-control-lg @error('password') is-invalid @enderror" 
                                    id="password" 
                                    name="password" 
                                    required 
                                    autocomplete="new-password"
                                    placeholder="Create a password"
                                >
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Confirm Password -->
                            <div class="mb-3">
                                <label for="password_confirmation" class="form-label fw-semibold">
                                    <i class="mdi mdi-lock-check me-2 text-muted"></i>Confirm Password
                                </label>
                                <input 
                                    type="password" 
                                    class="form-control form-control-lg" 
                                    id="password_confirmation" 
                                    name="password_confirmation" 
                                    required 
                                    autocomplete="new-password"
                                    placeholder="Confirm your password"
                                >
                            </div>

                            @if(!($invitation ?? null))
                            <!-- Currency Selection -->
                            <div class="mb-4">
                                <label for="currency_code" class="form-label fw-semibold">
                                    <i class="mdi mdi-currency-usd me-2 text-muted"></i>Currency
                                </label>
                                <select 
                                    class="form-select form-select-lg @error('currency_code') is-invalid @enderror" 
                                    id="currency_code" 
                                    name="currency_code" 
                                    required
                                >
                                    <option value="IQD" {{ old('currency_code', 'IQD') === 'IQD' ? 'selected' : '' }}>Iraqi Dinar (IQD)</option>
                                    <option value="USD" {{ old('currency_code') === 'USD' ? 'selected' : '' }}>US Dollar (USD)</option>
                                </select>
                                @error('currency_code')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            @else
                            <input type="hidden" name="currency_code" value="IQD">
                            @endif

                            <!-- Submit Button -->
                            <button type="submit" class="btn btn-primary btn-lg w-100 mb-3 fw-semibold" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none;">
                                <i class="mdi mdi-account-plus me-2"></i>Create Account
                            </button>
                        </form>

                        <!-- Login Link -->
                        <div class="text-center mt-4">
                            <p class="text-muted mb-0">
                                Already have an account? 
                                <a href="{{ route('login') }}" class="text-primary fw-semibold text-decoration-none">Sign in here</a>
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
