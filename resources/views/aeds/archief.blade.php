<x-app-layout>
    @push('styles')
        <!-- Bootstrap 5 CSS -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
        <!-- Bootstrap Icons -->
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    @endpush

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('AED Archief') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="container">

                {{-- Page Header --}}
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <a href="{{ route('aeds.index') }}" class="btn btn-outline-secondary btn-sm mb-2">
                            <i class="bi bi-arrow-left me-1"></i> Terug naar overzicht
                        </a>
                        <h1 class="h2 fw-bold mb-0">AED Archief</h1>
                        <p class="text-muted mb-0">Gearchiveerde AED's die uit roulatie zijn gehaald.</p>
                    </div>
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
                        <input type="text" class="form-control" id="searchArchief" placeholder="Zoek op eigenaar, adres of plaats..." onkeyup="filterAeds()">
                    </div>
                </div>

                {{-- Grid of Archived AED Cards --}}
                <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4" id="aedGridArchief">
                    @forelse($aeds as $aed)
                    <div class="col aed-item">
                        <div class="card h-100 shadow-sm border-dark">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <h5 class="card-title fw-bold text-primary mb-0">AED-{{ str_pad($aed->id, 3, '0', STR_PAD_LEFT) }}</h5>
                                    <span class="badge bg-dark">Archief</span>
                                </div>
                                <p class="card-text mb-1"><strong>Eigenaar:</strong> {{ $aed->eigenaar }}</p>
                                <p class="card-text text-muted mb-3">{{ $aed->adres }} {{ $aed->huisnummer }}, {{ $aed->plaats }}</p>
                                <div class="d-flex gap-2">
                                    <a href="{{ route('aeds.show', $aed) }}" class="btn btn-outline-primary btn-sm flex-fill">
                                        <i class="bi bi-eye me-1"></i> details
                                    </a>
                                </div>
                            </div>
                            <div class="card-footer bg-light">
                                <small class="text-muted">
                                    <i class="bi bi-clock-history me-1"></i> Gearchiveerd op: {{ $aed->updated_at->format('d-m-Y') }}
                                </small>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="col-12 text-center text-muted py-5">
                        <i class="bi bi-archive display-1"></i>
                        <p class="mt-3">Geen gearchiveerde AED's gevonden.</p>
                        <a href="{{ route('aeds.index') }}" class="btn btn-outline-primary mt-2">
                            <i class="bi bi-arrow-left me-1"></i> Terug naar overzicht
                        </a>
                    </div>
                    @endforelse
                </div>

            </div>
        </div>
    </div>

    @push('scripts')
    <script>
    function filterAeds() {
        const query = document.getElementById('searchArchief').value.toLowerCase();

        const items = document.querySelectorAll('#aedGridArchief .aed-item');
        items.forEach(item => {
            const text = item.innerText.toLowerCase();
            item.style.display = text.includes(query) ? '' : 'none';
        });
    }
    </script>
    @endpush
</x-app-layout>

