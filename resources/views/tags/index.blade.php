@extends('layouts.app')

@section('title', 'Tags')

@section('breadcrumbs')
    <li class="breadcrumb-item active">Tags</li>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Tags</h4>
        </div>
        <p class="text-muted mb-4">Create tags to label and organize your transactions (e.g., "tax-deductible", "reimbursable", "business").</p>
    </div>
</div>

<div class="row">
    <div class="col-lg-5">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Create New Tag</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('tags.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Tag Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" placeholder="e.g., Tax Deductible" required maxlength="50">
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Color</label>
                        <div class="d-flex align-items-center gap-2">
                            <input type="color" name="color" class="form-control form-control-color" value="{{ old('color', '#405189') }}">
                            <div class="d-flex gap-1">
                                @foreach(['#405189', '#0ab39c', '#f06548', '#f7b84b', '#299cdb', '#6c757d', '#e83e8c', '#6610f2'] as $preset)
                                    <button type="button" class="btn btn-sm border rounded-circle p-0" style="width:24px;height:24px;background:{{ $preset }}" onclick="this.closest('form').querySelector('[name=color]').value='{{ $preset }}'"></button>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary">
                        <i data-feather="plus" class="align-middle me-1"></i> Create Tag
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-7">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Your Tags ({{ $tags->count() }})</h5>
            </div>
            <div class="card-body">
                @if($tags->isEmpty())
                    <div class="text-center py-4">
                        <i data-feather="tag" style="width:48px;height:48px;" class="text-muted mb-3"></i>
                        <h5 class="text-muted">No tags yet</h5>
                        <p class="text-muted">Create your first tag to start organizing transactions.</p>
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-nowrap mb-0">
                            <thead>
                                <tr>
                                    <th>Tag</th>
                                    <th>Transactions</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($tags as $tag)
                                    <tr>
                                        <td>
                                            <span class="badge" style="background-color: {{ $tag->color }}; font-size: 0.85em;">{{ $tag->name }}</span>
                                        </td>
                                        <td>{{ $tag->transactions_count }}</td>
                                        <td class="text-end">
                                            <div class="d-flex gap-1 justify-content-end">
                                                <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editTag{{ $tag->id }}" title="Edit">
                                                    <i data-feather="edit-2" style="width:14px;height:14px;"></i>
                                                </button>
                                                <button type="button" class="btn btn-sm btn-outline-danger" title="Delete"
                                                    data-bs-toggle="modal" data-bs-target="#confirmModal"
                                                    data-action="{{ route('tags.destroy', $tag) }}"
                                                    data-message="Delete this tag? It will be removed from all transactions.">
                                                    <i data-feather="trash-2" style="width:14px;height:14px;"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>

                                    <!-- Edit Modal -->
                                    <div class="modal fade" id="editTag{{ $tag->id }}" tabindex="-1">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <form action="{{ route('tags.update', $tag) }}" method="POST">
                                                    @csrf
                                                    @method('PUT')
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Edit Tag</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="mb-3">
                                                            <label class="form-label">Tag Name</label>
                                                            <input type="text" name="name" class="form-control" value="{{ $tag->name }}" required maxlength="50">
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label">Color</label>
                                                            <input type="color" name="color" class="form-control form-control-color" value="{{ $tag->color }}">
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                        <button type="submit" class="btn btn-primary">Save Changes</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<x-confirm-modal />
@endsection
