<x-app-layout>
    @push('styles')
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    @endpush

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Controle - AED') }}
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
                    <span class="badge fs-6 bg-{{ $aed->status === 'actief' ? 'success' : ($aed->status === 'inactief' ? 'danger' : ($aed->status === 'archief' ? 'dark' : 'secondary')) }}">
                        {{ ucfirst($aed->status) }}
                    </span>
                </div>

                @if (session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white fw-bold">
                        <i class="bi bi-clipboard-check me-2"></i>CONTROLE INVULLEN
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('aeds.controle.store', $aed) }}">
                            @csrf

                            <div class="row g-4">
                                <div class="col-lg-6">
                                    <div class="card border-0 shadow-sm mb-4">
                                        <div class="card-header bg-light fw-bold">
                                            <i class="bi bi-calendar-event me-2"></i>Controle gegevens
                                        </div>
                                        <div class="card-body">
                                            <div class="mb-3">
                                                <label class="form-label">Datum controle</label>
                                                <input type="date" class="form-control" name="datum" value="{{ old('datum', now()->toDateString()) }}" required>
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label">Status</label>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="storing" id="storing" value="1" {{ old('storing', false) ? 'checked' : '' }}>

                                                    <label class="form-check-label" for="storing">
                                                        <i class="bi bi-exclamation-triangle text-warning me-1"></i>Storing
                                                    </label>
                                                </div>
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label">Bevindingen</label>
                                                <textarea class="form-control" name="bevindingen" rows="3">{{ old('bevindingen') }}</textarea>
                                            </div>

                                            <div class="mb-0">
                                                <label class="form-label">Bijzonderheden</label>
                                                <textarea class="form-control" name="bijzonderheden" rows="3">{{ old('bijzonderheden') }}</textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-lg-6">
                                    <div class="card border-0 shadow-sm mb-4">
                                        <div class="card-header bg-light fw-bold">
                                            <i class="bi bi-tools me-2"></i>Vervang/nieuw datum
                                        </div>
                                        <div class="card-body">
                                            <div class="mb-3">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="update_batterij_vervaldatum" id="update_batterij_vervaldatum" value="1" {{ old('update_batterij_vervaldatum') ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="update_batterij_vervaldatum">
                                                        Batterij vervaldatum aanpassen
                                                    </label>
                                                </div>

<div id="batterij-date-wrap" class="mt-2 {{ old('update_batterij_vervaldatum') ? '' : 'hidden' }}">
                                                    <label class="form-label">Nieuwe batterij vervaldatum</label>
                                                    <input type="date" class="form-control" name="batterij_vervaldatum" value="{{ old('batterij_vervaldatum', optional($aed->batterij_vervaldatum)->format('Y-m-d')) }}" {{ old('update_batterij_vervaldatum') ? '' : 'disabled' }}>
                                                </div>
                                            </div>

                                            <div class="mb-0">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="update_elektroden_vervaldatum" id="update_elektroden_vervaldatum" value="1" {{ old('update_elektroden_vervaldatum') ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="update_elektroden_vervaldatum">
                                                        Elektroden vervaldatum aanpassen
                                                    </label>
                                                </div>

<div id="elektroden-date-wrap" class="mt-2 {{ old('update_elektroden_vervaldatum') ? '' : 'hidden' }}">
                                                    <label class="form-label">Nieuwe elektroden vervaldatum</label>
                                                    <input type="date" class="form-control" name="elektroden_vervaldatum" value="{{ old('elektroden_vervaldatum', optional($aed->elektroden_vervaldatum)->format('Y-m-d')) }}" {{ old('update_elektroden_vervaldatum') ? '' : 'disabled' }}>
                                                </div>
                                            </div>

                                        </div>
                                    </div>

                                    <div class="d-grid">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="bi bi-save me-2"></i>Opslaan & loggen
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            function toggleWrap(checkboxId, wrapId) {
                const cb = document.getElementById(checkboxId);
                const wrap = document.getElementById(wrapId);
                const input = wrap.querySelector('input[type="date"]');

                if (!cb || !wrap || !input) return;

                const show = cb.checked;
wrap.classList.toggle('hidden', !show);
                input.disabled = !show;
            }

            document.addEventListener('DOMContentLoaded', function () {
                toggleWrap('update_batterij_vervaldatum', 'batterij-date-wrap');
                toggleWrap('update_elektroden_vervaldatum', 'elektroden-date-wrap');

                document.getElementById('update_batterij_vervaldatum')?.addEventListener('change', function () {
                    toggleWrap('update_batterij_vervaldatum', 'batterij-date-wrap');
                });

                document.getElementById('update_elektroden_vervaldatum')?.addEventListener('change', function () {
                    toggleWrap('update_elektroden_vervaldatum', 'elektroden-date-wrap');
                });
            });
        </script>
    @endpush
</x-app-layout>

