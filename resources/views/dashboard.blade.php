<x-app-layout>
    @push('styles')
        <!-- Bootstrap 5 CSS -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
        <!-- Bootstrap Icons -->
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    @endpush

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (Auth::user()?->is_admin)
                <div class="mb-4">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start gap-3">
                                <div>
                                    <div class="d-flex align-items-center gap-2 mb-1">
                                        <i class="bi bi-bell-fill text-primary"></i>
                                        <div class="fw-bold h5 mb-0">Recente melding</div>
                                    </div>

                                    <div class="text-muted">
                                        @if ($recentUnread)
                                            <span class="{{ $recentUnread->gelezen ? 'fw-normal' : 'fw-bold' }}">
                                                {{ $recentUnread->type === 'batterij' ? 'Batterij' : 'Elektroden' }}
                                                vervangen bij AED-{{ str_pad($recentUnread->aed_id, 3, '0', STR_PAD_LEFT) }}
                                            </span>
                                        @else
                                            Geen nieuwe meldingen.
                                        @endif
                                    </div>

                                    @if ($recentUnread)
                                        <div class="mt-2">
                                            <div><span class="fw-semibold">Bericht:</span> {{ $recentUnread->bericht }}</div>
                                            <div class="text-muted small">Datum: {{ optional($recentUnread->datum)->format('Y-m-d') }}</div>
                                        </div>
                                    @endif
                                </div>

                                <div class="text-end">
                                    <a href="{{ route('admin.notifications') }}" class="btn btn-primary btn-sm">
                                        <i class="bi bi-list-ul me-1"></i> Meldingenoverzicht
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="text-muted">
                        Welkom op je dashboard.
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
