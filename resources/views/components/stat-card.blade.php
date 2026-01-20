<div class="col-xl-3 col-md-6">
    <div class="card">
        <div class="card-body">
            <div class="d-flex">
                <div class="flex-1">
                    <p class="text-truncate font-size-14 mb-2">{{ $title }}</p>
                    <h4 class="mb-2">{{ $value }}</h4>
                </div>
                <div class="avatar-sm">
                    <span class="avatar-title bg-{{ $color ?? 'primary' }}-subtle text-{{ $color ?? 'primary' }} rounded">
                        <i class="{{ $icon ?? 'bx bx-dollar' }} font-size-24"></i>
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>
