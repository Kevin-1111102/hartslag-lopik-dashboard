<x-app-layout>
    @push('styles')
        <!-- Bootstrap 5 CSS -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
        <!-- Bootstrap Icons -->
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    @endpush

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Controle geschiedenis - AED') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="container">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <a href="{{ route('aeds.show', $aed) }}" class="btn btn-outline-secondary btn-sm mb-2">
                            <i class="bi bi-arrow-left me-1"></i> Terug naar AED
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

                <div class="card shadow-sm">
                    <div class="card-header bg-info text-white fw-bold">
                        <i class="bi bi-clock-history me-2"></i>VOLLEDIGE CONTROLEHISTORIE
                    </div>

                    <div class="card-body">
                        @if ($logs->isEmpty())
                            <div class="text-center text-muted py-5">
                                <i class="bi bi-list-check display-4 mb-2"></i>
                                <div class="fw-bold mb-1">Nog geen controles gevonden.</div>
                                <div>Voer een controle in via het controle-scherm.</div>
                                <a href="{{ route('aeds.controle.show', $aed) }}" class="btn btn-outline-primary mt-3">
                                    <i class="bi bi-pencil-square me-2"></i>Controle invoeren
                                </a>
                            </div>
                        @else
                            <div class="table-responsive">
                                <table class="table table-hover align-middle">
                                    <thead>
                                    <tr>
                                        <th>Datum</th>
                                        <th>Controleur</th>
                                        <th>Storing</th>
                                        <th>Bevindingen</th>
                                        <th>Bijzonderheden</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach ($logs as $log)
                                        <tr>
                                            <td class="text-nowrap">{{ optional($log->datum)->format('Y-m-d') }}</td>
                                            <td>{{ $log->user->name ?? 'Onbekend' }}</td>
                                            <td>
                                                @if ($log->storing)
                                                    <span class="text-warning fw-bold">⚠ Storing</span>
                                                @else
                                                    <span class="text-success fw-bold">✓ In orde</span>
                                                @endif
                                            </td>
                                            <td class="text-wrap" style="max-width: 250px;">
                                                {{ $log->bevindingen ?? '-' }}
                                            </td>
                                            <td class="text-wrap" style="max-width: 300px;">
                                                {{ $log->bijzonderheden ?? '-' }}
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    @endpush
</x-app-layout>

