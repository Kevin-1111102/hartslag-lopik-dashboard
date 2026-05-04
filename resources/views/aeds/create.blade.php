<x-app-layout>
    @push('styles')
        <!-- Bootstrap 5 CSS -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
        <!-- Bootstrap Icons -->
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    @endpush

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Nieuwe AED Aanmelden') }}
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
                        <h1 class="h2 fw-bold mb-0">Nieuwe AED Aanmelden</h1>
                        <p class="text-muted mb-0">Vul alle benodigde gegevens in om een nieuwe AED te registreren.</p>
                    </div>
                </div>

                {{-- Validation Errors --}}
                @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <strong><i class="bi bi-exclamation-triangle-fill me-2"></i>Er zijn fouten gevonden:</strong>
                        <ul class="mb-0 mt-2">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Sluiten"></button>
                    </div>
                @endif

                <form method="POST" action="{{ route('aeds.store') }}">
                    @csrf

                    <div class="row g-4">

                        {{-- Left Column --}}
                        <div class="col-lg-6">

                            {{-- Locatiegegevens --}}
                            <div class="card shadow-sm mb-4">
                                <div class="card-header bg-primary text-white fw-bold">
                                    <i class="bi bi-geo-alt me-2"></i>LOCATIE GEGEVENS
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label for="adres" class="form-label fw-semibold">Adres <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('adres') is-invalid @enderror" id="adres" name="adres" value="{{ old('adres') }}" required placeholder="Bijv. Dorpsstraat">
                                        @error('adres')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="mb-3">
                                        <label for="huisnummer" class="form-label fw-semibold">Huisnummer <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('huisnummer') is-invalid @enderror" id="huisnummer" name="huisnummer" value="{{ old('huisnummer') }}" required placeholder="Bijv. 12">
                                        @error('huisnummer')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="mb-3">
                                        <label for="plaats" class="form-label fw-semibold">Plaats <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('plaats') is-invalid @enderror" id="plaats" name="plaats" value="{{ old('plaats') }}" required placeholder="Bijv. Lopik">
                                        @error('plaats')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="mb-0">
                                        <label for="beschrijving" class="form-label fw-semibold">Beschrijving locatie</label>
                                        <textarea class="form-control @error('beschrijving') is-invalid @enderror" id="beschrijving" name="beschrijving" rows="2" placeholder="Bijv. Achter de receptie">{{ old('beschrijving') }}</textarea>
                                        @error('beschrijving')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            {{-- AED Specificaties --}}
                            <div class="card shadow-sm mb-4">
                                <div class="card-header bg-primary text-white fw-bold">
                                    <i class="bi bi-heart-pulse me-2"></i>AED SPECIFICATIES
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label for="aed_type" class="form-label fw-semibold">AED type <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('aed_type') is-invalid @enderror" id="aed_type" name="aed_type" value="{{ old('aed_type') }}" required placeholder="Bijv. Philips HeartStart FRx">
                                        @error('aed_type')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="mb-3">
                                        <label for="serienummer_aed" class="form-label fw-semibold">Serienummer AED</label>
                                        <input type="text" class="form-control @error('serienummer_aed') is-invalid @enderror" id="serienummer_aed" name="serienummer_aed" value="{{ old('serienummer_aed') }}" placeholder="Bijv. 12345678">
                                        @error('serienummer_aed')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="mb-3">
                                        <label for="serienummer_kast" class="form-label fw-semibold">Serienummer kast</label>
                                        <input type="text" class="form-control @error('serienummer_kast') is-invalid @enderror" id="serienummer_kast" name="serienummer_kast" value="{{ old('serienummer_kast') }}" placeholder="Bijv. K987654">
                                        @error('serienummer_kast')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="mb-0">
                                        <label for="serienummer" class="form-label fw-semibold">Serienummer (algemeen)</label>
                                        <input type="text" class="form-control @error('serienummer') is-invalid @enderror" id="serienummer" name="serienummer" value="{{ old('serienummer') }}" placeholder="Optioneel">
                                        @error('serienummer')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            {{-- Beveiliging --}}
                            <div class="card shadow-sm">
                                <div class="card-header bg-secondary text-white fw-bold">
                                    <i class="bi bi-shield-lock me-2"></i>BEVEILIGING
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label for="security" class="form-label fw-semibold">Security / Toegang</label>
                                        <input type="text" class="form-control @error('security') is-invalid @enderror" id="security" name="security" value="{{ old('security') }}" placeholder="Bijv. Sleutelkluis, Code slot">
                                        @error('security')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="mb-3">
                                        <label for="pincode" class="form-label fw-semibold">Pincode</label>
                                        <input type="text" class="form-control @error('pincode') is-invalid @enderror" id="pincode" name="pincode" value="{{ old('pincode') }}" placeholder="Bijv. 1234">
                                        <div class="form-text">Wordt versleuteld opgeslagen.</div>
                                        @error('pincode')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="mb-0">
                                        <label for="onderhoudscode" class="form-label fw-semibold">Onderhoudscode</label>
                                        <input type="text" class="form-control @error('onderhoudscode') is-invalid @enderror" id="onderhoudscode" name="onderhoudscode" value="{{ old('onderhoudscode') }}" placeholder="Bijv. 5678">
                                        <div class="form-text">Wordt versleuteld opgeslagen.</div>
                                        @error('onderhoudscode')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                        </div>
                        {{-- End Left Column --}}

                        {{-- Right Column --}}
                        <div class="col-lg-6">

                            {{-- Eigenaar / Contact --}}
                            <div class="card shadow-sm mb-4">
                                <div class="card-header bg-primary text-white fw-bold">
                                    <i class="bi bi-person me-2"></i>EIGENAAR / CONTACT
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label for="eigenaar" class="form-label fw-semibold">Eigenaar <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('eigenaar') is-invalid @enderror" id="eigenaar" name="eigenaar" value="{{ old('eigenaar') }}" required placeholder="Bijv. Gemeente Lopik">
                                        @error('eigenaar')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="mb-3">
                                        <label for="contactpersoon" class="form-label fw-semibold">Contactpersoon</label>
                                        <input type="text" class="form-control @error('contactpersoon') is-invalid @enderror" id="contactpersoon" name="contactpersoon" value="{{ old('contactpersoon') }}" placeholder="Naam hoofdcontactpersoon">
                                        @error('contactpersoon')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="mb-0">
                                        <label for="lokaal_contactpersoon" class="form-label fw-semibold">Lokaal contactpersoon</label>
                                        <input type="text" class="form-control @error('lokaal_contactpersoon') is-invalid @enderror" id="lokaal_contactpersoon" name="lokaal_contactpersoon" value="{{ old('lokaal_contactpersoon') }}" placeholder="Naam lokale contactpersoon">
                                        @error('lokaal_contactpersoon')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            {{-- Status & Vervaldatums --}}
                            <div class="card shadow-sm mb-4">
                                <div class="card-header bg-success text-white fw-bold">
                                    <i class="bi bi-calendar-check me-2"></i>STATUS & VERVALDATUMS
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label for="status" class="form-label fw-semibold">Status <span class="text-danger">*</span></label>
                                        <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                                            <option value="actief" {{ old('status', 'actief') === 'actief' ? 'selected' : '' }}>Actief</option>
                                            <option value="inactief" {{ old('status') === 'inactief' ? 'selected' : '' }}>Inactief</option>
                                            <option value="vervangen" {{ old('status') === 'vervangen' ? 'selected' : '' }}>Vervangen</option>
                                        </select>
                                        @error('status')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="mb-3">
                                        <label for="batterij_vervaldatum" class="form-label fw-semibold">Batterij vervaldatum</label>
                                        <input type="date" class="form-control @error('batterij_vervaldatum') is-invalid @enderror" id="batterij_vervaldatum" name="batterij_vervaldatum" value="{{ old('batterij_vervaldatum') }}">
                                        @error('batterij_vervaldatum')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="mb-0">
                                        <label for="elektroden_vervaldatum" class="form-label fw-semibold">Elektroden vervaldatum</label>
                                        <input type="date" class="form-control @error('elektroden_vervaldatum') is-invalid @enderror" id="elektroden_vervaldatum" name="elektroden_vervaldatum" value="{{ old('elektroden_vervaldatum') }}">
                                        @error('elektroden_vervaldatum')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            {{-- Opmerkingen --}}
                            <div class="card shadow-sm mb-4">
                                <div class="card-header bg-info text-white fw-bold">
                                    <i class="bi bi-chat-left-text me-2"></i>OPMERKINGEN
                                </div>
                                <div class="card-body">
                                    <div class="mb-0">
                                        <label for="opmerkingen" class="form-label fw-semibold">Opmerkingen</label>
                                        <textarea class="form-control @error('opmerkingen') is-invalid @enderror" id="opmerkingen" name="opmerkingen" rows="4" placeholder="Extra informatie over deze AED...">{{ old('opmerkingen') }}</textarea>
                                        @error('opmerkingen')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            {{-- Beheerafspraken --}}
                            <div class="card shadow-sm">
                                <div class="card-header bg-dark text-white fw-bold">
                                    <i class="bi bi-file-text me-2"></i>BEHEERAFSPRAKEN
                                </div>
                                <div class="card-body">
                                    <div class="form-check form-switch mb-3 pb-3 border-bottom">
                                        <input type="hidden" name="beheerafspraak[is_beheerder]" value="0">
                                        <input class="form-check-input" type="checkbox" id="is_beheerder" name="beheerafspraak[is_beheerder]" value="1" {{ old('beheerafspraak.is_beheerder') ? 'checked' : '' }}>
                                        <label class="form-check-label fw-semibold" for="is_beheerder">
                                            Stichting Hartslag Lopik is beheerder
                                        </label>
                                    </div>
                                    <div class="form-check form-switch mb-3 pb-3 border-bottom">
                                        <input type="hidden" name="beheerafspraak[voert_controles_uit]" value="0">
                                        <input class="form-check-input" type="checkbox" id="voert_controles_uit" name="beheerafspraak[voert_controles_uit]" value="1" {{ old('beheerafspraak.voert_controles_uit') ? 'checked' : '' }}>
                                        <label class="form-check-label fw-semibold" for="voert_controles_uit">
                                            Voert periodieke controles uit
                                        </label>
                                    </div>
                                    <div class="form-check form-switch mb-3 pb-3 border-bottom">
                                        <input type="hidden" name="beheerafspraak[beheert_in_hartslagnu]" value="0">
                                        <input class="form-check-input" type="checkbox" id="beheert_in_hartslagnu" name="beheerafspraak[beheert_in_hartslagnu]" value="1" {{ old('beheerafspraak.beheert_in_hartslagnu') ? 'checked' : '' }}>
                                        <label class="form-check-label fw-semibold" for="beheert_in_hartslagnu">
                                            Beheert in HartslagNu
                                        </label>
                                    </div>
                                    <div class="form-check form-switch mb-0">
                                        <input type="hidden" name="beheerafspraak[extern_onderhoud]" value="0">
                                        <input class="form-check-input" type="checkbox" id="extern_onderhoud" name="beheerafspraak[extern_onderhoud]" value="1" {{ old('beheerafspraak.extern_onderhoud') ? 'checked' : '' }}>
                                        <label class="form-check-label fw-semibold" for="extern_onderhoud">
                                            Extern onderhoud
                                        </label>
                                </div>
                            </div>

                        </div>
                        {{-- End Right Column --}}

                    </div>
                    {{-- End Row --}}

                    {{-- Submit Buttons --}}
                    <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
                        <a href="{{ route('aeds.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-x-lg me-1"></i> Annuleren
                        </a>
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="bi bi-check-lg me-2"></i> AED Opslaan
                        </button>
                    </div>

                </form>

            </div>
        </div>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    @endpush
</x-app-layout>

