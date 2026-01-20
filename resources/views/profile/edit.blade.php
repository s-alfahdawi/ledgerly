@extends('layouts.app')

@section('title', 'Profile')

@section('content')
<div class="row">
    <div class="col-xl-12">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title mb-4">Profile Information</h4>
                @include('profile.partials.update-profile-information-form')
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-xl-12">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title mb-4">Update Password</h4>
                @include('profile.partials.update-password-form')
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-xl-12">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title mb-4">Delete Account</h4>
                @include('profile.partials.delete-user-form')
            </div>
        </div>
    </div>
</div>
@endsection
