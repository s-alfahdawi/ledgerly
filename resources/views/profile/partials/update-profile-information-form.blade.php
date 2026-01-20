<form method="post" action="{{ route('profile.update') }}">
    @csrf
    @method('patch')

    <div class="mb-3">
        <label for="name" class="form-label">Name</label>
        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $user->name) }}" required autofocus>
        @error('name')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="mb-3">
        <label for="email" class="form-label">Email</label>
        <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $user->email) }}" required>
        @error('email')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror

        @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
            <div class="mt-2">
                <p class="text-muted small">
                    {{ __('Your email address is unverified.') }}
                    <form id="send-verification" method="post" action="{{ route('verification.send') }}" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-link p-0 align-baseline">{{ __('Click here to re-send the verification email.') }}</button>
                    </form>
                </p>
                @if (session('status') === 'verification-link-sent')
                    <p class="text-success small mt-2">{{ __('A new verification link has been sent to your email address.') }}</p>
                @endif
            </div>
        @endif
    </div>

    <div class="mb-3">
        <button type="submit" class="btn btn-primary">{{ __('Save') }}</button>
        @if (session('status') === 'profile-updated')
            <span class="text-success ms-2">{{ __('Saved.') }}</span>
        @endif
    </div>
</form>
