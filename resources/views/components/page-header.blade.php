@php
    $accountContext = app(\App\Services\AccountContext::class);
    $account = $accountContext->account();
    $currencyCode = $account?->currency_code ?? 'IQD';
@endphp

<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">@yield('page-title', 'Page')</h4>
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
