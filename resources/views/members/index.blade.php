@extends('layouts.app')

@section('title', 'Members')

@php
    $accountContext = app(\App\Services\AccountContext::class);
    $account = $accountContext->account();
@endphp

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Workspace Members</h4>
        </div>
        <p class="text-muted mb-4">Invite team members to collaborate on your workspace. Assign roles (Admin, Member, Viewer) to control access and permissions.</p>
    </div>
</div>

<div class="row">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Invite Member</h4>
            </div>
            <div class="card-body">
                <form action="{{ route('members.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Email <span class="text-danger">*</span></label>
                        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" placeholder="user@example.com" required>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">
                            @if(config('mail.default') === 'log')
                                <i class="mdi mdi-information text-warning"></i> 
                                <strong>Note:</strong> Emails are currently being logged to <code>storage/logs/laravel.log</code>. 
                                To send real emails, configure SMTP in your <code>.env</code> file.
                            @else
                                If the user is not registered, they will receive an email invitation with a registration link.
                            @endif
                        </small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Role <span class="text-danger">*</span></label>
                        <select name="role" class="form-select @error('role') is-invalid @enderror" required>
                            <option value="admin">Admin - Full access to manage workspace</option>
                            <option value="member" selected>Member - Can create and edit transactions</option>
                            <option value="viewer">Viewer - Read-only access</option>
                        </select>
                        @error('role')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <button type="submit" class="btn btn-primary">
                            <i data-feather="user-plus" class="align-middle me-1"></i> Invite Member
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Current Members</h4>
            </div>
            <div class="card-body">
                @if($members->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-nowrap table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Role</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($members as $member)
                                    <tr>
                                        <td><strong>{{ $member->name }}</strong></td>
                                        <td>{{ $member->email }}</td>
                                        <td>
                                            <span class="badge bg-{{ $member->pivot->role === 'owner' ? 'primary' : ($member->pivot->role === 'admin' ? 'info' : 'secondary') }}">
                                                {{ ucfirst($member->pivot->role) }}
                                            </span>
                                        </td>
                                        <td class="text-end">
                                            @if($member->pivot->role !== 'owner')
                                                <form action="{{ route('members.destroy', $member) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to remove this member?')">
                                                        <i data-feather="user-x" class="icon-xs"></i> Remove
                                                    </button>
                                                </form>
                                            @else
                                                <span class="text-muted small">Owner</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-muted mb-0">No members found.</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
