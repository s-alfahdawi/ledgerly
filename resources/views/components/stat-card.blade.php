@props([
    'title' => '',
    'value' => '',
    'color' => 'primary',
    'icon' => 'bx bx-dollar',
    'trend' => null,
    'trendValue' => null,
    'sparkline' => null,
])

<div class="col-xl-3 col-md-6">
    <div class="card">
        <div class="card-body">
            <div class="d-flex">
                <div class="flex-1">
                    <p class="text-truncate font-size-14 mb-2">{{ $title }}</p>
                    <h4 class="mb-2">{{ $value }}</h4>
                    @if($trend !== null && $trendValue !== null)
                        <p class="text-muted mb-0">
                            <span class="text-{{ $trend === 'up' ? 'success' : 'danger' }} me-1">
                                <i class="mdi mdi-arrow-{{ $trend === 'up' ? 'up' : 'down' }}-bold"></i>
                                {{ $trendValue }}
                            </span>
                            <span class="font-size-12">vs last month</span>
                        </p>
                    @endif
                </div>
                <div class="avatar-sm">
                    <span class="avatar-title bg-{{ $color }}-subtle text-{{ $color }} rounded">
                        <i class="{{ $icon }} font-size-24"></i>
                    </span>
                </div>
            </div>
            @if($sparkline)
                <div class="mt-2" id="sparkline-{{ Str::slug($title) }}" style="height: 35px;"></div>
                @push('scripts')
                <script>
                    (function() {
                        var opts = {
                            chart: { type: 'area', height: 35, sparkline: { enabled: true } },
                            series: [{ data: {!! json_encode($sparkline) !!} }],
                            stroke: { curve: 'smooth', width: 2 },
                            colors: ['{{ $color === "success" ? "#0ab39c" : ($color === "danger" ? "#f06548" : "#405189") }}'],
                            fill: { type: 'gradient', gradient: { shadeIntensity: 1, opacityFrom: 0.3, opacityTo: 0.1 } },
                            tooltip: { enabled: false },
                        };
                        new ApexCharts(document.querySelector('#sparkline-{{ Str::slug($title) }}'), opts).render();
                    })();
                </script>
                @endpush
            @endif
        </div>
    </div>
</div>
