@extends('layouts.app')

@section('title', 'API Tokens')

@section('content')
@include('components.page-header')
@section('page-title', 'API Tokens')

<div class="row">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Create Token</h4>
            </div>
            <div class="card-body">
                <form action="{{ route('tokens.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Token Name</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Create</button>
                </form>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Your Tokens</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Created</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($tokens as $token)
                                <tr>
                                    <td>{{ $token->name }}</td>
                                    <td>{{ $token->created_at->format('M d, Y') }}</td>
                                    <td>
                                        <form action="{{ route('tokens.destroy', $token->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Revoke</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3">No tokens created yet</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
