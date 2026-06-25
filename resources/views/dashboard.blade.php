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

            {{-- ELEKTRODEN/BATTERIJ STATUS BLOKKEN --}}
            @php
                $warningIcon = 'bi bi-exclamation-triangle';
            @endphp

            <div class="row g-4">
                {{-- Batterij expired --}}
                <div class="col-12 col-lg-4">
                    <div class="card shadow-sm border-danger border-2 h-100">
                        <div class="card-header bg-danger text-white fw-bold">
                            <i class="bi bi-battery me-2"></i> BATTERIJ - VERVALLEN
                        </div>
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <a href="{{ route('dashboard.aeds.batterij-expired') }}" class="fw-bold text-decoration-none">
                                    {{ $batterijExpired->count() }} AED's
                                </a>
                            </div>
                            @forelse($batterijExpired->take(5) as $aed)
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <a href="{{ route('aeds.show', $aed) }}" class="text-decoration-none">
                                        AED-{{ str_pad($aed->id, 3, '0', STR_PAD_LEFT) }}
                                    </a>
                                    <span class="text-danger fw-bold">{{ optional($aed->batterij_vervaldatum)->format('Y-m-d') ?? '-' }}</span>
                                </div>
                            @empty
                                <div class="text-muted">Geen AED's met vervallen batterij.</div>
                            @endforelse
                        </div>
                    </div>
                </div>

                {{-- Batterij warning --}}
                <div class="col-12 col-lg-4">
                    <div class="card shadow-sm border-warning border-2 h-100">
                        <div class="card-header bg-warning text-dark fw-bold">
                            <i class="bi bi-battery-warning me-2"></i> BATTERIJ - BINNENKORT
                        </div>
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <a href="{{ route('dashboard.aeds.batterij-warning') }}" class="fw-bold text-decoration-none">
                                    {{ $batterijWarning->count() }} AED's
                                </a>
                            </div>
                            @forelse($batterijWarning->take(5) as $aed)
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <a href="{{ route('aeds.show', $aed) }}" class="text-decoration-none">
                                        AED-{{ str_pad($aed->id, 3, '0', STR_PAD_LEFT) }}
                                    </a>
                                    <span class="text-warning fw-bold">{{ optional($aed->batterij_vervaldatum)->format('Y-m-d') ?? '-' }}</span>
                                </div>
                            @empty
                                <div class="text-muted">Geen AED's met batterij die binnen 60 dagen vervalt.</div>
                            @endforelse
                        </div>
                    </div>
                </div>

                {{-- Batterij goed --}}
                <div class="col-12 col-lg-4">
                    <div class="card shadow-sm border-success border-2 h-100">
                        <div class="card-header bg-success text-white fw-bold">
                            <i class="bi bi-battery-full me-2"></i> BATTERIJ - GOED
                        </div>
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <a href="{{ route('dashboard.aeds.batterij-goed') }}" class="fw-bold text-decoration-none">
                                    {{ $batterijGoed->count() }} AED's
                                </a>
                            </div>
                            @forelse($batterijGoed->take(5) as $aed)
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <a href="{{ route('aeds.show', $aed) }}" class="text-decoration-none">
                                        AED-{{ str_pad($aed->id, 3, '0', STR_PAD_LEFT) }}
                                    </a>
                                    <span class="text-success fw-bold">{{ optional($aed->batterij_vervaldatum)->format('Y-m-d') ?? '-' }}</span>
                                </div>
                            @empty
                                <div class="text-muted">Geen AED's met batterij in goede staat.</div>
                            @endforelse
                        </div>
                    </div>
                </div>

                {{-- Elektroden expired --}}
                <div class="col-12 col-lg-4">
                    <div class="card shadow-sm border-danger border-2 h-100">
                        <div class="card-header bg-danger text-white fw-bold">
                            <i class="bi bi-lightning me-2"></i> ELEKTRODEN - VERVALLEN
                        </div>
                        <div class="card-body">
                            <div class="mb-2">
                                <a href="{{ route('dashboard.aeds.elektroden-expired') }}" class="fw-bold text-decoration-none">
                                    {{ $elektrodenExpired->count() }} AED's
                                </a>
                            </div>
                            @forelse($elektrodenExpired->take(5) as $aed)
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <a href="{{ route('aeds.show', $aed) }}" class="text-decoration-none">
                                        AED-{{ str_pad($aed->id, 3, '0', STR_PAD_LEFT) }}
                                    </a>
                                    <span class="text-danger fw-bold">{{ optional($aed->elektroden_vervaldatum)->format('Y-m-d') ?? '-' }}</span>
                                </div>
                            @empty
                                <div class="text-muted">Geen AED's met vervallen elektroden.</div>
                            @endforelse
                        </div>
                    </div>
                </div>

                {{-- Elektroden warning --}}
                <div class="col-12 col-lg-4">
                    <div class="card shadow-sm border-warning border-2 h-100">
                        <div class="card-header bg-warning text-dark fw-bold">
                            <i class="bi bi-lightning-charge me-2"></i> ELEKTRODEN - BINNENKORT
                        </div>
                        <div class="card-body">
                            <div class="mb-2">
                                <a href="{{ route('dashboard.aeds.elektroden-warning') }}" class="fw-bold text-decoration-none">
                                    {{ $elektrodenWarning->count() }} AED's
                                </a>
                            </div>
                            @forelse($elektrodenWarning->take(5) as $aed)
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <a href="{{ route('aeds.show', $aed) }}" class="text-decoration-none">
                                        AED-{{ str_pad($aed->id, 3, '0', STR_PAD_LEFT) }}
                                    </a>
                                    <span class="text-warning fw-bold">{{ optional($aed->elektroden_vervaldatum)->format('Y-m-d') ?? '-' }}</span>
                                </div>
                            @empty
                                <div class="text-muted">Geen AED's met elektroden die binnen 60 dagen vervalt.</div>
                            @endforelse
                        </div>
                    </div>
                </div>

                {{-- Elektroden goed --}}
                <div class="col-12 col-lg-4">
                    <div class="card shadow-sm border-success border-2 h-100">
                        <div class="card-header bg-success text-white fw-bold">
                            <i class="bi bi-lightning-charge-fill me-2"></i> ELEKTRODEN - GOED
                        </div>
                        <div class="card-body">
                            <div class="mb-2">
                                <a href="{{ route('dashboard.aeds.elektroden-goed') }}" class="fw-bold text-decoration-none">
                                    {{ $elektrodenGoed->count() }} AED's
                                </a>
                            </div>
                            @forelse($elektrodenGoed->take(5) as $aed)
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <a href="{{ route('aeds.show', $aed) }}" class="text-decoration-none">
                                        AED-{{ str_pad($aed->id, 3, '0', STR_PAD_LEFT) }}
                                    </a>
                                    <span class="text-success fw-bold">{{ optional($aed->elektroden_vervaldatum)->format('Y-m-d') ?? '-' }}</span>
                                </div>
                            @empty
                                <div class="text-muted">Geen AED's met elektroden in goede staat.</div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

            {{-- eventueel oude welkom text behouden? --}}
            <div class="card shadow-sm mt-4">
                <div class="card-body">
                    <div class="text-muted">Controleer de statusblokken voor batterij en elektroden.</div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
