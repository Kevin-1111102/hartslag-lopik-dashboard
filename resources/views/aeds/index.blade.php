<x-app-layout>
    @push('styles')
        <!-- Bootstrap 5 CSS -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
        <!-- Bootstrap Icons -->
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    @endpush

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('AED Overzicht') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="container">


    {{-- Mobile Layout (sidebar-style list) --}}
    <div class="d-md-none">
        {{-- Search Bar --}}
        <div class="mb-3">
            <input type="text" class="form-control" id="searchMobile" placeholder="zoek aed" onkeyup="filterAeds()">
        </div>

        {{-- Scrollable List of AED Cards --}}
        <div class="list-group" id="aedListMobile">
            @forelse($aeds as $aed)
            <div class="list-group-item aed-item mb-2 rounded shadow-sm">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <h5 class="mb-1 fw-bold text-primary">AED-{{ str_pad($aed->id, 3, '0', STR_PAD_LEFT) }}</h5>
                </div>
                <p class="mb-1"><strong>Eigenaar:</strong> {{ $aed->eigenaar }}</p>
                <p class="mb-2 text-muted">{{ $aed->adres }} {{ $aed->huisnummer }}</p>
                <div class="d-flex gap-2">
                    <a href="{{ route('aeds.show', $aed) }}" class="btn btn-outline-primary btn-sm flex-fill">details</a>
                    <a href="#" class="btn btn-outline-success btn-sm flex-fill">controle</a>
                </div>
            </div>
            @empty
            <div class="list-group-item text-center text-muted">
                Geen AEDs gevonden.
            </div>
            @endforelse
        </div>
    </div>

    {{-- Desktop Layout (grid of cards) --}}
    <div class="d-none d-md-block">
        {{-- Header --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h2 fw-bold">AED Overzicht</h1>
                <p class="text-muted mb-0">Zoek en beheer alle AED's in de regio</p>
            </div>
            <button class="btn btn-outline-secondary" >
                <i class="bi bi-download me-1"></i> aeds exporteren
            </button>
        </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Sluiten"></button>
        </div>
    @endif

    {{-- Search Bar --}}
        <div class="mb-4">
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-search"></i></span>
                <input type="text" class="form-control" id="searchDesktop" placeholder="Zoek op eigenaar, adres of plaats..." onkeyup="filterAeds()">
            </div>
        </div>

        {{-- Grid of AED Cards --}}
        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4" id="aedGridDesktop">
            @forelse($aeds as $aed)
            <div class="col aed-item">
                <div class="card h-100 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <h5 class="card-title fw-bold text-primary mb-0">AED-{{ str_pad($aed->id, 3, '0', STR_PAD_LEFT) }}</h5>
                        <span class="badge bg-{{ $aed->status === 'actief' ? 'success' : ($aed->status === 'inactief' ? 'danger' : ($aed->status === 'archief' ? 'dark' : 'warning')) }}">
                                {{ ucfirst($aed->status) }}
                            </span>
                        </div>
                        <p class="card-text mb-1"><strong>Eigenaar:</strong> {{ $aed->eigenaar }}</p>
                        <p class="card-text text-muted mb-3">{{ $aed->adres }} {{ $aed->huisnummer }}, {{ $aed->plaats }}</p>
                        <div class="d-flex gap-2">
                            <a href="{{ route('aeds.show', $aed) }}" class="btn btn-outline-primary btn-sm flex-fill">
                                <i class="bi bi-eye me-1"></i> details
                            </a>
                            <a href="#" class="btn btn-outline-success btn-sm flex-fill">
                                <i class="bi bi-check-circle me-1"></i> controle
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-12 text-center text-muted py-5">
                <i class="bi bi-heart-pulse display-1"></i>
                <p class="mt-3">Geen AEDs gevonden.</p>
            </div>
            @endforelse
        </div>

        {{-- Bottom Buttons --}}
        <div class="d-flex justify-content-between mt-4">
            <a href="{{ route('aeds.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg me-1"></i> nieuwe aed aanmelden
            </a>
            <a href="{{ route('aeds.archief') }}" class="btn btn-outline-dark">
                <i class="bi bi-archive me-1"></i> archief
            </a>
        </div>
    </div>

            </div>
        </div>
    </div>

    @push('scripts')
    <script>
    function filterAeds() {
        const mobileQuery = document.getElementById('searchMobile').value.toLowerCase();
        const desktopQuery = document.getElementById('searchDesktop').value.toLowerCase();
        const query = mobileQuery || desktopQuery;

        // Filter mobile items
        const mobileItems = document.querySelectorAll('#aedListMobile .aed-item');
        mobileItems.forEach(item => {
            const text = item.innerText.toLowerCase();
            item.style.display = text.includes(query) ? '' : 'none';
        });

        // Filter desktop items
        const desktopItems = document.querySelectorAll('#aedGridDesktop .aed-item');
        desktopItems.forEach(item => {
            const text = item.innerText.toLowerCase();
            item.style.display = text.includes(query) ? '' : 'none';
        });
    }
    </script>
    @endpush
</x-app-layout>
