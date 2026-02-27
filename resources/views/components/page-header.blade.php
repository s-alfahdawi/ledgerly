<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <div>
                <h4 class="mb-sm-0">@yield('page-title', 'Page')</h4>
                @hasSection('breadcrumbs')
                    <nav aria-label="breadcrumb" class="mt-1">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                            @yield('breadcrumbs')
                        </ol>
                    </nav>
                @endhasSection
            </div>
            <div class="page-title-right">
                @hasSection('page-actions')
                    @yield('page-actions')
                @endif
            </div>
        </div>
        @hasSection('page-description')
            <p class="text-muted mb-3">@yield('page-description')</p>
        @endif
    </div>
</div>
