<x-app-layout>
    @push('styles')
        <!-- Bootstrap 5 CSS -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    @endpush

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('AED Bewerken') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="container">

                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <a href="{{ route('aeds.show', $aed) }}" class="btn btn-outline-secondary btn-sm mb-2">
                            <i class="bi bi-arrow-left me-1"></i> Terug naar details
                        </a>
                        <h1 class="h2 fw-bold mb-0">AED-{{ str_pad($aed->id, 3, '0', STR_PAD_LEFT) }} bewerken</h1>
                        <p class="text-muted mb-0">Pas de gegevens aan en sla op.</p>
                    </div>
                </div>

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

                <form method="POST" action="{{ route('aeds.update', $aed) }}" enctype="multipart/form-data">
                    @csrf
                    @method('PATCH')

                    <div class="row g-4">

                        <div class="col-lg-6">
                            <div class="card shadow-sm mb-4">
                                <div class="card-header bg-primary text-white fw-bold">
                                    <i class="bi bi-geo-alt me-2"></i>LOCATIE GEGEVENS
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label for="adres" class="form-label fw-semibold">Adres <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('adres') is-invalid @enderror" id="adres" name="adres" value="{{ old('adres', $aed->adres) }}" required>
                                        @error('adres')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="mb-3">
                                        <label for="huisnummer" class="form-label fw-semibold">Huisnummer <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('huisnummer') is-invalid @enderror" id="huisnummer" name="huisnummer" value="{{ old('huisnummer', $aed->huisnummer) }}" required>
                                        @error('huisnummer')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="mb-3">
                                        <label for="plaats" class="form-label fw-semibold">Plaats <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('plaats') is-invalid @enderror" id="plaats" name="plaats" value="{{ old('plaats', $aed->plaats) }}" required>
                                        @error('plaats')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="mb-0">
                                        <label for="beschrijving" class="form-label fw-semibold">Beschrijving locatie</label>
                                        <textarea class="form-control @error('beschrijving') is-invalid @enderror" id="beschrijving" name="beschrijving" rows="2">{{ old('beschrijving', $aed->beschrijving) }}</textarea>
                                        @error('beschrijving')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="card shadow-sm mb-4">
                                <div class="card-header bg-primary text-white fw-bold">
                                    <i class="bi bi-heart-pulse me-2"></i>AED SPECIFICATIES
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label for="aed_type" class="form-label fw-semibold">AED type <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('aed_type') is-invalid @enderror" id="aed_type" name="aed_type" value="{{ old('aed_type', $aed->aed_type) }}" required>
                                        @error('aed_type')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="mb-3">
                                        <label for="serienummer_aed" class="form-label fw-semibold">Serienummer AED</label>
                                        <input type="text" class="form-control @error('serienummer_aed') is-invalid @enderror" id="serienummer_aed" name="serienummer_aed" value="{{ old('serienummer_aed', $aed->serienummer_aed) }}">
                                        @error('serienummer_aed')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="mb-3">
                                        <label for="serienummer_kast" class="form-label fw-semibold">Serienummer kast</label>
                                        <input type="text" class="form-control @error('serienummer_kast') is-invalid @enderror" id="serienummer_kast" name="serienummer_kast" value="{{ old('serienummer_kast', $aed->serienummer_kast) }}">
                                        @error('serienummer_kast')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="mb-0">
                                        <label for="serienummer" class="form-label fw-semibold">Serienummer (algemeen)</label>
                                        <input type="text" class="form-control @error('serienummer') is-invalid @enderror" id="serienummer" name="serienummer" value="{{ old('serienummer', $aed->serienummer) }}">
                                        @error('serienummer')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="card shadow-sm">
                                <div class="card-header bg-secondary text-white fw-bold">
                                    <i class="bi bi-shield-lock me-2"></i>BEVEILIGING
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label for="security" class="form-label fw-semibold">Security / Toegang</label>
                                        <input type="text" class="form-control @error('security') is-invalid @enderror" id="security" name="security" value="{{ old('security', $aed->security) }}">
                                        @error('security')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="mb-3">
                                        <label for="pincode" class="form-label fw-semibold">Pincode</label>
                                        <input type="text" class="form-control @error('pincode') is-invalid @enderror" id="pincode" name="pincode" value="{{ old('pincode', '') }}" placeholder="Nieuw (wordt versleuteld opgeslagen)">
                                        <div class="form-text">Laat leeg als je niets wilt wijzigen.</div>
                                        @error('pincode')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="mb-0">
                                        <label for="onderhoudscode" class="form-label fw-semibold">Onderhoudscode</label>
                                        <input type="text" class="form-control @error('onderhoudscode') is-invalid @enderror" id="onderhoudscode" name="onderhoudscode" value="{{ old('onderhoudscode', '') }}" placeholder="Nieuw (wordt versleuteld opgeslagen)">
                                        <div class="form-text">Laat leeg als je niets wilt wijzigen.</div>
                                        @error('onderhoudscode')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="card shadow-sm mb-4">
                                <div class="card-header bg-primary text-white fw-bold">
                                    <i class="bi bi-person me-2"></i>EIGENAAR / CONTACT
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label for="eigenaar" class="form-label fw-semibold">Eigenaar <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('eigenaar') is-invalid @enderror" id="eigenaar" name="eigenaar" value="{{ old('eigenaar', $aed->eigenaar) }}" required>
                                        @error('eigenaar')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="mb-3">
                                        <label for="contactpersoon" class="form-label fw-semibold">Contactpersoon</label>
                                        <input type="text" class="form-control @error('contactpersoon') is-invalid @enderror" id="contactpersoon" name="contactpersoon" value="{{ old('contactpersoon', $aed->contactpersoon) }}">
                                        @error('contactpersoon')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="mb-0">
                                        <label for="lokaal_contactpersoon" class="form-label fw-semibold">Lokaal contactpersoon</label>
                                        <input type="text" class="form-control @error('lokaal_contactpersoon') is-invalid @enderror" id="lokaal_contactpersoon" name="lokaal_contactpersoon" value="{{ old('lokaal_contactpersoon', $aed->lokaal_contactpersoon) }}">
                                        @error('lokaal_contactpersoon')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="card shadow-sm mb-4">
                                <div class="card-header bg-success text-white fw-bold">
                                    <i class="bi bi-calendar-check me-2"></i>STATUS & VERVALDATUMS
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label for="status" class="form-label fw-semibold">Status <span class="text-danger">*</span></label>
                                        <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                                            @foreach(['actief'=>'Actief','inactief'=>'Inactief','vervangen'=>'Vervangen','archief'=>'Archief'] as $value => $label)
                                                <option value="{{ $value }}" {{ old('status', $aed->status) === $value ? 'selected' : '' }}>{{ $label }}</option>
                                            @endforeach
                                        </select>
                                        @error('status')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="mb-3">
                                        <label for="batterij_vervaldatum" class="form-label fw-semibold">Batterij vervaldatum</label>
                                        <input type="date" class="form-control @error('batterij_vervaldatum') is-invalid @enderror" id="batterij_vervaldatum" name="batterij_vervaldatum" value="{{ old('batterij_vervaldatum', optional($aed->batterij_vervaldatum)->format('Y-m-d')) }}">
                                        @error('batterij_vervaldatum')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="mb-0">
                                        <label for="elektroden_vervaldatum" class="form-label fw-semibold">Elektroden vervaldatum</label>
                                        <input type="date" class="form-control @error('elektroden_vervaldatum') is-invalid @enderror" id="elektroden_vervaldatum" name="elektroden_vervaldatum" value="{{ old('elektroden_vervaldatum', optional($aed->elektroden_vervaldatum)->format('Y-m-d')) }}">
                                        @error('elektroden_vervaldatum')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="card shadow-sm mb-4">
                                <div class="card-header bg-primary text-white fw-bold">
                                    <i class="bi bi-images me-2"></i>FOTO VAN DE AED
                                </div>
                                <div class="card-body">
                                    @if($aed->photos && $aed->photos->count() > 0)
                                        @php
                                            $existingPhoto = $aed->photos->first();
                                        @endphp

                                        <div class="mb-3">
                                            <div class="position-relative border rounded p-1 bg-light" style="max-width: 320px;">
                                                <img
                                                    src="{{ asset('storage/' . $existingPhoto->path) }}"
                                                    alt="AED foto"
                                                    class="img-fluid rounded"
                                                    style="height: 180px; object-fit: cover; width: 100%;">

                                                <div class="position-absolute top-0 end-0 m-2">
                                                    <button type="submit"
                                                        name="remove_photo"
                                                        value="1"
                                                        class="btn btn-sm btn-danger rounded-circle photo-remove-btn"
                                                        style="width: 34px; height: 34px; display:flex; align-items:center; justify-content:center;"
                                                        aria-label="Verwijder foto"
                                                        title="Verwijder foto">
                                                        ×
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="form-text">Klik op × om deze foto te verwijderen.</div>
                                        </div>
                                    @endif

                                    <div class="mb-0">
                                        <label for="foto" class="form-label fw-semibold">Nieuwe foto (optioneel)</label>
                                        <input type="file"
                                            class="form-control @error('foto') is-invalid @enderror"
                                            id="foto"
                                            name="foto"
                                            accept="image/*">

                                        <div class="form-text">Laat leeg om de huidige foto te behouden. Als je upload vervangt dit de huidige foto.</div>

                                        @error('foto')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror

                                        <div class="mt-2">
                                            <div class="form-text mb-2">
                                                <strong>Geselecteerd:</strong> <span id="fotoPreviewName" class="text-muted">—</span>
                                            </div>

                                            <div id="fotoPreview"
                                                class="position-relative border rounded p-1 bg-light"
                                                style="max-width: 320px; display: none;">

                                                <img
                                                    id="fotoPreviewImg"
                                                    src="#"
                                                    alt="Preview"
                                                    class="img-fluid rounded"
                                                    style="height: 180px; object-fit: cover; width: 100%; display: block;">

                                                <button
                                                    type="button"
                                                    id="fotoRemoveBtn"
                                                    class="btn btn-sm btn-danger position-absolute top-0 end-0 m-2 rounded-circle"
                                                    aria-label="Verwijderen"
                                                    title="Verwijderen"
                                                    style="display:block;">
                                                    ×
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="card shadow-sm mb-4">
                                <div class="card-header bg-info text-white fw-bold">
                                    <i class="bi bi-file-earmark-text me-2"></i>DOCUMENT SAMENWERKING
                                </div>
                                <div class="card-body">
                                    <div class="mb-0">
                                        <label for="cooperation_agreement" class="form-label fw-semibold">Samenwerkingsovereenkomst (optioneel)</label>
                                        <input type="file" class="form-control @error('cooperation_agreement') is-invalid @enderror" id="cooperation_agreement" name="cooperation_agreement" accept=".pdf,.doc,.docx,.odt,.rtf">
                                        <div class="form-text">Upload hier de ondertekende overeenkomst tussen de AED-eigenaar en de stichting. Laat leeg om de huidige te behouden.</div>
                                        @error('cooperation_agreement')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="card shadow-sm mb-4">
                                <div class="card-header bg-info text-white fw-bold">
                                    <i class="bi bi-chat-left-text me-2"></i>OPMERKINGEN
                                </div>
                                <div class="card-body">
                                    <div class="mb-0">
                                        <label for="opmerkingen" class="form-label fw-semibold">Opmerkingen</label>
                                        <textarea class="form-control @error('opmerkingen') is-invalid @enderror" id="opmerkingen" name="opmerkingen" rows="4" placeholder="Extra informatie over deze AED...">{{ old('opmerkingen', $aed->opmerkingen) }}</textarea>
                                        @error('opmerkingen')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="card shadow-sm">
                                <div class="card-header bg-dark text-white fw-bold">
                                    <i class="bi bi-file-text me-2"></i>BEHEERAFSPRAKEN
                                </div>
                                <div class="card-body">
                                    @php
                                        $ba = $aed->beheerafspraak;
                                    @endphp

                                    <div class="form-check form-switch mb-3 pb-3 border-bottom">
                                        <input type="hidden" name="beheerafspraak[is_beheerder]" value="0">
                                        <input class="form-check-input" type="checkbox" id="is_beheerder" name="beheerafspraak[is_beheerder]" value="1" {{ old('beheerafspraak.is_beheerder', $ba?->is_beheerder) ? 'checked' : '' }}>
                                        <label class="form-check-label fw-semibold" for="is_beheerder">Stichting Hartslag Lopik is beheerder</label>
                                    </div>
                                    <div class="form-check form-switch mb-3 pb-3 border-bottom">
                                        <input type="hidden" name="beheerafspraak[voert_controles_uit]" value="0">
                                        <input class="form-check-input" type="checkbox" id="voert_controles_uit" name="beheerafspraak[voert_controles_uit]" value="1" {{ old('beheerafspraak.voert_controles_uit', $ba?->voert_controles_uit) ? 'checked' : '' }}>
                                        <label class="form-check-label fw-semibold" for="voert_controles_uit">Voert periodieke controles uit</label>
                                    </div>
                                    <div class="form-check form-switch mb-3 pb-3 border-bottom">
                                        <input type="hidden" name="beheerafspraak[beheert_in_hartslagnu]" value="0">
                                        <input class="form-check-input" type="checkbox" id="beheert_in_hartslagnu" name="beheerafspraak[beheert_in_hartslagnu]" value="1" {{ old('beheerafspraak.beheert_in_hartslagnu', $ba?->beheert_in_hartslagnu) ? 'checked' : '' }}>
                                        <label class="form-check-label fw-semibold" for="beheert_in_hartslagnu">Beheert in HartslagNu</label>
                                    </div>
                                    <div class="form-check form-switch mb-0">
                                        <input type="hidden" name="beheerafspraak[extern_onderhoud]" value="0">
                                        <input class="form-check-input" type="checkbox" id="extern_onderhoud" name="beheerafspraak[extern_onderhoud]" value="1" {{ old('beheerafspraak.extern_onderhoud', $ba?->extern_onderhoud) ? 'checked' : '' }}>
                                        <label class="form-check-label fw-semibold" for="extern_onderhoud">Extern onderhoud</label>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>

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

        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const fotoInput = document.getElementById('foto');
                const fotoPreview = document.getElementById('fotoPreview');
                const fotoPreviewImg = document.getElementById('fotoPreviewImg');
                const fotoPreviewName = document.getElementById('fotoPreviewName');
                const fotoRemoveBtn = document.getElementById('fotoRemoveBtn');

                function hidePreview() {
                    if (!fotoPreview) return;
                    fotoPreview.style.display = 'none';
                    if (fotoPreviewImg) fotoPreviewImg.src = '#';
                    if (fotoPreviewName) fotoPreviewName.textContent = '—';
                }

                function showPreview(file) {
                    if (!file) return;

                    const reader = new FileReader();
                    reader.onload = function (e) {
                        if (fotoPreviewImg) fotoPreviewImg.src = e.target.result;
                        if (fotoPreview) fotoPreview.style.display = 'block';
                        if (fotoPreviewName) fotoPreviewName.textContent = file.name || '';
                    };
                    reader.readAsDataURL(file);
                }

                hidePreview();

                if (fotoInput) {
                    fotoInput.addEventListener('change', (e) => {
                        const file = e.target.files && e.target.files[0] ? e.target.files[0] : null;

                        if (file) {
                            showPreview(file);
                        } else {
                            hidePreview();
                        }
                    });
                }

                if (fotoRemoveBtn) {
                    fotoRemoveBtn.addEventListener('click', () => {
                        if (fotoInput) fotoInput.value = '';
                        hidePreview();
                    });
                }
            });
        </script>
    @endpush
</x-app-layout>

