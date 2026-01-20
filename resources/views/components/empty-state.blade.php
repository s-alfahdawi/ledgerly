<div class="text-center py-5">
    <i class="{{ $icon ?? 'bx bx-inbox' }} font-size-48 text-muted"></i>
    <h5 class="mt-3">{{ $title ?? 'No data found' }}</h5>
    <p class="text-muted">{{ $message ?? 'Get started by creating your first item.' }}</p>
    @if(isset($action))
        <a href="{{ $action['url'] }}" class="btn btn-primary mt-2">{{ $action['label'] }}</a>
    @endif
</div>
