<x-guest-layout>
    <div class="container py-5">
        <div class="row justify-content-center align-items-center" style="min-height: 100vh;">
            <div class="col-md-8 col-lg-6 col-xl-5">
                <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
                    <div class="card-header bg-primary border-0 py-4" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                        <div class="text-center text-white">
                            <i class="mdi mdi-shield-lock" style="font-size: 3rem;"></i>
                            <h3 class="fw-bold mt-3 mb-1">Confirm Password</h3>
                            <p class="mb-0 opacity-75">This is a secure area. Please confirm your password.</p>
                        </div>
                    </div>

                    <div class="card-body p-4 p-md-5">
                        <form method="POST" action="{{ route('password.confirm') }}">
                            @csrf

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

                            <button type="submit" class="btn btn-primary btn-lg w-100 fw-semibold" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none;">
                                <i class="mdi mdi-check me-2"></i>Confirm
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-guest-layout>
