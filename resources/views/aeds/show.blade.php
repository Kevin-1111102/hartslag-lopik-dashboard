    <x-app-layout>
        @push('styles')
            <!-- Bootstrap 5 CSS -->
            <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
            <!-- Bootstrap Icons -->
            <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
        @endpush

        <x-slot name="header">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('AED Details') }}
            </h2>
        </x-slot>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="container">

                    @php
                        $batterijDays = $aed->batterij_vervaldatum ? (int) round(now()->diffInDays($aed->batterij_vervaldatum, false)) : null;
                        $batterijExpired = $batterijDays !== null && $batterijDays < 0;
                        $batterijWarning = $batterijDays !== null && $batterijDays >= 0 && $batterijDays <= 60;

                        $elektrodenDays = $aed->elektroden_vervaldatum ? (int) round(now()->diffInDays($aed->elektroden_vervaldatum, false)) : null;
                        $elektrodenExpired = $elektrodenDays !== null && $elektrodenDays < 0;
                        $elektrodenWarning = $elektrodenDays !== null && $elektrodenDays >= 0 && $elektrodenDays <= 60;

                        $actieVereist = $batterijExpired || $batterijWarning || $elektrodenExpired || $elektrodenWarning;
                    @endphp

                    {{-- HEADER --}}
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div>
                            <a href="{{ route('aeds.index') }}" class="btn btn-outline-secondary btn-sm mb-2">
                                <i class="bi bi-arrow-left me-1"></i> Terug naar overzicht
                            </a>
                            <h1 class="h2 fw-bold mb-0">AED-{{ str_pad($aed->id, 3, '0', STR_PAD_LEFT) }}</h1>
                            <p class="text-muted mb-0">{{ $aed->adres }} {{ $aed->huisnummer }}, {{ $aed->plaats }}</p>
                        </div>
                        <span class="badge fs-6 bg-{{
                            $aed->status === 'actief'   ? 'success' :
                            ($aed->status === 'inactief' ? 'danger'  :
                            ($aed->status === 'archief'  ? 'dark'    : 'secondary'))
                        }}">
                            {{ ucfirst($aed->status) }}
                        </span>
                    </div>

                    {{-- ARCHIEF BANNER --}}
                    @if($aed->status === 'archief')
                        <div class="alert alert-dark d-flex align-items-center mb-4" role="alert">
                            <i class="bi bi-archive-fill me-2 fs-5"></i>
                            <div>
                                <strong>Deze AED is gearchiveerd</strong> – de AED is uit roulatie gehaald. De volledige historie is nog beschikbaar. Alleen admins kunnen deze AED permanent verwijderen.
                            </div>
                        </div>
                    @endif

                    <div class="row g-4">

                        {{-- LINKER KOLOM --}}
                        <div class="col-md-6">

                            {{-- Locatie --}}
                            <div class="card shadow-sm mb-4">
                                <div class="card-header bg-primary text-white fw-bold">
                                    <i class="bi bi-geo-alt me-2"></i>LOCATIE GEGEVENS
                                </div>
                                <div class="card-body">
                                    <p class="mb-2"><strong>Adres:</strong> {{ $aed->adres }} {{ $aed->huisnummer }}</p>
                                    <p class="mb-2"><strong>Plaats:</strong> {{ $aed->plaats }}</p>
                                    @if($aed->beschrijving)
                                        <p class="mb-0"><strong>Beschrijving:</strong> {{ $aed->beschrijving }}</p>
                                    @endif
                                </div>
                            </div>

                            {{-- AED Specificaties --}}
                            <div class="card shadow-sm mb-4">
                                <div class="card-header bg-primary text-white fw-bold">
                                    <i class="bi bi-heart-pulse me-2"></i>AED SPECIFICATIES
                                </div>
                                <div class="card-body">
                                    <p class="mb-2"><strong>AED type:</strong> {{ $aed->aed_type }}</p>
                                    <p class="mb-2"><strong>Serienummer AED:</strong> {{ $aed->serienummer_aed ?? '-' }}</p>
                                    <p class="mb-0"><strong>Serienummer kast:</strong> {{ $aed->serienummer_kast ?? '-' }}</p>
                                </div>
                            </div>

                            {{-- Eigenaar / Beheerder --}}
                            <div class="card shadow-sm mb-4">
                                <div class="card-header bg-primary text-white fw-bold">
                                    <i class="bi bi-person me-2"></i>EIGENAAR / BEHEERDER
                                </div>
                                <div class="card-body">
                                    <p class="mb-2"><strong>Eigenaar:</strong> {{ $aed->eigenaar }}</p>
                                    <p class="mb-2"><strong>Contactpersoon:</strong> {{ $aed->contactpersoon ?? '-' }}</p>
                                    <p class="mb-0"><strong>Lokaal contactpersoon:</strong> {{ $aed->lokaal_contactpersoon ?? '-' }}</p>
                                </div>
                            </div>

                            {{-- Beheerafspraken --}}
                            <div class="card shadow-sm">
                                <div class="card-header bg-primary text-white fw-bold d-flex justify-content-between align-items-center">
                                    <span><i class="bi bi-file-text me-2"></i>BEHEERAFSPRAKEN</span>
                                    @can('admin')
                                        <button class="btn btn-light btn-sm">
                                            <i class="bi bi-pencil me-1"></i> bewerken
                                        </button>
                                    @endcan
                                </div>
                                <div class="card-body">
                                    @if($aed->beheerafspraak)
                                        <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom">
                                            <span>Stichting Hartslag Lopik is beheerder</span>
                                            <span class="fs-5">{{ $aed->beheerafspraak->is_beheerder ? '✓' : '✗' }}</span>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom">
                                            <span>Voert periodieke controles uit</span>
                                            <span class="fs-5">{{ $aed->beheerafspraak->voert_controles_uit ? '✓' : '✗' }}</span>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom">
                                            <span>Beheert in HartslagNu</span>
                                            <span class="fs-5">{{ $aed->beheerafspraak->beheert_in_hartslagnu ? '✓' : '✗' }}</span>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span>Extern onderhoud</span>
                                            <span class="fs-5">{{ $aed->beheerafspraak->extern_onderhoud ? '✓' : '✗' }}</span>
                                        </div>
                                    @else
                                        <p class="text-muted mb-0">Geen beheerafspraken gevonden.</p>
                                    @endif
                                </div>
                            </div>

                        </div>{{-- einde linker kolom --}}

                        {{-- RECHTER KOLOM --}}
                        <div class="col-md-6">

                            {{-- Actie vereist --}}
                            @if($actieVereist)
                                <div class="card shadow-sm mb-4 border-warning border-2">
                                    <div class="card-header bg-warning text-dark fw-bold">
                                        <i class="bi bi-exclamation-triangle me-2"></i>ACTIE VEREIST
                                    </div>
                                    <div class="card-body bg-warning bg-opacity-10">
                                        @if($batterijExpired)
                                            <p class="fw-bold text-danger mb-2">
                                                <i class="bi bi-battery me-2"></i>Batterij is vervallen – Vervang onmiddellijk!
                                            </p>
                                        @elseif($batterijWarning)
                                            <p class="fw-bold text-warning mb-2">
                                                <i class="bi bi-battery me-2"></i>Batterij vervalt binnenkort – Vervang binnen {{ $batterijDays }} dagen
                                            </p>
                                        @endif

                                        @if($elektrodenExpired)
                                            <p class="fw-bold text-danger mb-0">
                                                <i class="bi bi-lightning me-2"></i>Elektroden zijn vervallen – Vervang onmiddellijk!
                                            </p>
                                        @elseif($elektrodenWarning)
                                            <p class="fw-bold text-warning mb-0">
                                                <i class="bi bi-lightning me-2"></i>Elektroden vervallen binnenkort – Vervang binnen {{ $elektrodenDays }} dagen
                                            </p>
                                        @endif
                                    </div>
                                </div>
                            @endif

                            {{-- Batterij status --}}
                            <div class="card shadow-sm mb-4">
                                <div class="card-header bg-{{ $batterijExpired ? 'danger' : ($batterijWarning ? 'warning' : 'success') }} text-{{ $batterijExpired || $batterijWarning ? 'dark' : 'white' }} fw-bold">
                                    <i class="bi bi-battery-full me-2"></i>BATTERIJ STATUS
                                </div>
                                <div class="card-body">
                                    <p class="mb-2">
                                        <strong>Vervaldatum:</strong>
                                        <span class="text-{{ $batterijExpired ? 'danger' : ($batterijWarning ? 'warning' : 'success') }} fw-bold">
                                            {{ $aed->batterij_vervaldatum ? $aed->batterij_vervaldatum->format('Y-m-d') : '-' }}
                                        </span>
                                    </p>
                                    <p class="mb-2">
                                        <strong>Resterende dagen:</strong>
                                        @if($batterijDays !== null)
                                            <span class="badge bg-{{ $batterijExpired ? 'danger' : ($batterijWarning ? 'warning' : 'success') }}">
                                                {{ $batterijExpired ? abs($batterijDays) . ' dagen vervallen' : $batterijDays . ' dagen' }}
                                            </span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </p>
                                    <p class="mb-0">
                                        <strong>Laatste vervanging:</strong> <span class="text-muted">-</span>
                                    </p>
                                </div>
                            </div>

                            {{-- Elektroden status --}}
                            <div class="card shadow-sm mb-4">
                                <div class="card-header bg-{{ $elektrodenExpired ? 'danger' : ($elektrodenWarning ? 'warning' : 'success') }} text-{{ $elektrodenExpired || $elektrodenWarning ? 'dark' : 'white' }} fw-bold">
                                    <i class="bi bi-lightning me-2"></i>ELEKTRODEN STATUS
                                </div>
                                <div class="card-body">
                                    <p class="mb-2">
                                        <strong>Vervaldatum:</strong>
                                        <span class="text-{{ $elektrodenExpired ? 'danger' : ($elektrodenWarning ? 'warning' : 'success') }} fw-bold">
                                            {{ $aed->elektroden_vervaldatum ? $aed->elektroden_vervaldatum->format('Y-m-d') : '-' }}
                                        </span>
                                    </p>
                                    <p class="mb-2">
                                        <strong>Resterende dagen:</strong>
                                        @if($elektrodenDays !== null)
                                            <span class="badge bg-{{ $elektrodenExpired ? 'danger' : ($elektrodenWarning ? 'warning' : 'success') }}">
                                                {{ $elektrodenExpired ? abs($elektrodenDays) . ' dagen vervallen' : $elektrodenDays . ' dagen' }}
                                            </span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </p>
                                    <p class="mb-0">
                                        <strong>Laatste vervanging:</strong> <span class="text-muted">-</span>
                                    </p>
                                </div>
                            </div>

                            {{-- Laatste controle --}}
                            <div class="card shadow-sm mb-4">
                                <div class="card-header bg-info text-white fw-bold">
                                    <i class="bi bi-clipboard-check me-2"></i>LAATSTE CONTROLE
                                </div>
                                <div class="card-body">
                                    @if($latestControle)
                                        <p class="mb-2"><strong>Datum:</strong> {{ $latestControle->datum->format('Y-m-d') }}</p>
                                        <p class="mb-2"><strong>Controleur:</strong> {{ $latestControle->user->name ?? 'Onbekend' }}</p>
                                        <p class="mb-3">
                                            <strong>Status:</strong>
                                            @if($latestControle->storing)
                                                <span class="text-warning fw-bold">⚠ Storing</span>
                                            @else
                                                <span class="text-success fw-bold">✓ In orde</span>
                                            @endif
                                        </p>
                                        @if($latestControle->bevindingen)
                                            <p class="mb-2"><strong>Bevindingen:</strong> {{ $latestControle->bevindingen }}</p>
                                        @endif
                                        @if($latestControle->bijzonderheden)
                                            <p class="mb-3"><strong>Bijzonderheden:</strong> {{ $latestControle->bijzonderheden }}</p>
                                        @endif
                                        <a href="#" class="btn btn-outline-info btn-sm">
                                            <i class="bi bi-clock-history me-1"></i> volledige geschiedenis
                                        </a>
                                    @else
                                        <p class="text-muted mb-0">Nog geen controle uitgevoerd.</p>
                                    @endif
                                </div>
                            </div>

                            {{-- Beveiliging --}}
                            <div class="card shadow-sm mb-4">
                                <div class="card-header bg-secondary text-white fw-bold">
                                    <i class="bi bi-shield-lock me-2"></i>BEVEILIGING
                                </div>
                                <div class="card-body">
                                    <p class="mb-2"><strong>Security / Toegang:</strong> {{ $aed->security ?? '-' }}</p>
                                    <p class="mb-2"><strong>Pincode:</strong> {{ $aed->pincode ?? '-' }}</p>
                                    <p class="mb-0"><strong>Onderhoudscode:</strong> {{ $aed->onderhoudscode ?? '-' }}</p>
                                </div>
                            </div>

                            {{-- Snelle acties (alleen admin) --}}
                            @can('admin')
                                <div class="card shadow-sm">
                                    <div class="card-header bg-secondary text-white fw-bold">
                                        <i class="bi bi-lightning-charge me-2"></i>SNELLE ACTIE
                                    </div>
                                    <div class="card-body">
                                        <div class="d-grid gap-2">

                                            <button class="btn btn-outline-secondary">
                                                <i class="bi bi-download me-2"></i>exporteren
                                            </button>

                                            <button class="btn btn-outline-secondary">
                                                <i class="bi bi-pencil me-2"></i>bewerken
                                            </button>

    @if($aed->status !== 'archief')
                                                <form method="POST" action="{{ route('aeds.archive', $aed) }}">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="btn btn-outline-warning w-100"
                                                        onclick="return confirm('Weet je zeker dat je deze AED wilt archiveren? De AED wordt uit roulatie gehaald maar de historie blijft bewaard.')">
                                                        <i class="bi bi-archive me-2"></i>archiveer
                                                    </button>
                                                </form>
                                            @endif

                                            @if($aed->status === 'archief')
                                                <form method="POST" action="{{ route('aeds.unarchive', $aed) }}" class="d-block mb-2">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="btn btn-outline-success w-100"
                                                        onclick="return confirm('Weet je zeker dat je deze AED wilt de-archiveren en weer actief maken?')">
                                                        <i class="bi bi-arrow-repeat me-2"></i>de-archiveren
                                                    </button>
                                                </form>
                                            @endif

                                            @if($aed->status === 'archief')
                                                <button type="button" class="btn btn-outline-danger w-100"
                                                    data-bs-toggle="modal" data-bs-target="#deleteAedModal">
                                                    <i class="bi bi-trash me-2"></i>permanent verwijderen
                                                </button>
                                            @endif

                                        </div>
                                    </div>
                                </div>

                                {{-- Delete modal (alleen bij archief status, binnen admin check) --}}
                                @if($aed->status === 'archief')
                                    <div class="modal fade" id="deleteAedModal" tabindex="-1"
                                        aria-labelledby="deleteAedModalLabel" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content">
                                                <div class="modal-header bg-danger text-white">
                                                    <h5 class="modal-title" id="deleteAedModalLabel">
                                                        <i class="bi bi-exclamation-triangle me-2"></i>Permanente verwijdering
                                                    </h5>
                                                    <button type="button" class="btn-close btn-close-white"
                                                        data-bs-dismiss="modal" aria-label="Sluiten"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <p class="fw-bold">Weet je zeker dat je AED-{{ str_pad($aed->id, 3, '0', STR_PAD_LEFT) }} permanent wilt verwijderen?</p>
                                                    <p class="text-muted mb-0">Deze actie kan niet ongedaan worden gemaakt. Alle gegevens inclusief historie, controles en beheerafspraken worden verwijderd.</p>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-outline-secondary"
                                                        data-bs-dismiss="modal">Annuleren</button>
                                                    <form method="POST" action="{{ route('aeds.destroy', $aed) }}">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger">
                                                            Ja, permanent verwijderen
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif

                            @endcan

                        </div>{{-- einde rechter kolom --}}

                    </div>{{-- einde row --}}

                </div>{{-- einde container --}}
            </div>
        </div>

        @push('scripts')
            <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
        @endpush

    </x-app-layout>