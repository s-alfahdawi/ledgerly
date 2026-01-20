@extends('layouts.app')

@section('title', 'No Account')

@section('content')
<div class="row justify-content-center">
    <div class="col-xl-8">
        <div class="card">
            <div class="card-body text-center">
                <div class="mb-4">
                    <i data-feather="briefcase" class="icon-lg text-muted"></i>
                </div>
                <h4 class="card-title mb-3">No Account Found</h4>
                <p class="text-muted mb-4">
                    You don't have any accounts yet. An account (workspace) is required to manage your billing and transactions.
                </p>
                <p class="text-muted mb-4">
                    If you just registered, your account should have been created automatically. If you continue to see this message, please contact support.
                </p>
                <div class="mt-4">
                    <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="btn btn-primary">
                        Logout and Try Again
                    </a>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                        @csrf
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
