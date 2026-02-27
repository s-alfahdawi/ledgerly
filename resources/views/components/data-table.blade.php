@props([
    'id' => 'data-table',
    'striped' => false,
    'hover' => true,
    'responsive' => true,
    'emptyIcon' => 'inbox',
    'emptyTitle' => 'No data found',
    'emptyMessage' => '',
    'emptyAction' => null,
    'emptyActionLabel' => 'Get Started',
    'paginator' => null,
])

<div class="card">
    <div class="card-body">
        @if($responsive)
        <div class="table-responsive">
        @endif
            <table id="{{ $id }}" class="table table-nowrap{{ $hover ? ' table-hover' : '' }}{{ $striped ? ' table-striped' : '' }} mb-0">
                <thead class="table-light">
                    <tr>
                        {{ $head }}
                    </tr>
                </thead>
                <tbody>
                    {{ $slot }}
                </tbody>
            </table>
        @if($responsive)
        </div>
        @endif

        @if(isset($empty) && $empty)
            <div class="text-center py-5">
                <div class="text-muted">
                    <i data-feather="{{ $emptyIcon }}" class="icon-lg mb-2"></i>
                    <p class="mb-2 font-size-16">{{ $emptyTitle }}</p>
                    @if($emptyMessage)
                        <p class="text-muted mb-3">{{ $emptyMessage }}</p>
                    @endif
                    @if($emptyAction)
                        <a href="{{ $emptyAction }}" class="btn btn-primary">{{ $emptyActionLabel }}</a>
                    @endif
                </div>
            </div>
        @endif

        @if($paginator && $paginator->hasPages())
            <div class="d-flex justify-content-end mt-3">
                {{ $paginator->links() }}
            </div>
        @endif
    </div>
</div>
