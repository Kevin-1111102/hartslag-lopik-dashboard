<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            AED Details - {{ $aed->serienummer ?? 'Onbekend' }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <!-- AED Info -->
                    <div class="mb-8">
                        <h3 class="text-lg font-bold mb-4">AED Informatie</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <p><strong>Type:</strong> {{ $aed->type }}</p>
                            <p><strong>Serienummer:</strong> {{ $aed->serienummer ?? '-' }}</p>
                            <p><strong>Adres:</strong> {{ $aed->adres }} {{ $aed->huisnummer }}, {{ $aed->plaats }}</p>
                            <p><strong>Status:</strong> 
                                <span class="px-2 py-1 rounded text-xs font-bold {{ $aed->status === 'actief' ? 'bg-green-200 text-green-800' : ($aed->status === 'archief' ? 'bg-yellow-200 text-yellow-800' : 'bg-red-200 text-red-800') }}">
                                    {{ ucfirst($aed->status) }}
                                </span>
                            </p>
                            <p><strong>Batterij vervaldatum:</strong> {{ $aed->batterij_vervaldatum?->format('d-m-Y') ?? '-' }}</p>
                            <p><strong>Elektroden vervaldatum:</strong> {{ $aed->elektroden_vervaldatum?->format('d-m-Y') ?? '-' }}</p>
                            <p><strong>Beschrijving:</strong> {{ $aed->beschrijving ?? '-' }}</p>
                            <p><strong>Pincode:</strong> {{ $aed->pincode ?? '-' }}</p>
                        </div>
                    </div>

                    <!-- History / Controles -->
                    <div>
                        <h3 class="text-lg font-bold mb-4">Controle Geschiedenis ({{ $controles->count() }})</h3>
                        @if($controles->count() > 0)
                            <table class="min-w-full table-auto mb-4">
                                <thead>
                                    <tr>
                                        <th class="px-4 py-2 border">Datum</th>
                                        <th class="px-4 py-2 border">Status AED</th>
                                        <th class="px-4 py-2 border">Status Kast</th>
                                        <th class="px-4 py-2 border">Opmerkingen</th>
                                        <th class="px-4 py-2 border">Actie nodig</th>
                                        <th class="px-4 py-2 border">Gebruiker</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($controles as $controle)
                                        <tr>
                                            <td class="px-4 py-2 border">{{ $controle->controle_datum->format('d-m-Y') }}</td>
                                            <td class="px-4 py-2 border">{{ ucfirst(str_replace('_', ' ', $controle->status_aed)) }}</td>
                                            <td class="px-4 py-2 border">{{ $controle->status_kast ?? '-' }}</td>
                                            <td class="px-4 py-2 border">{{ Str::limit($controle->opmerkingen, 50) }}</td>
                                            <td class="px-4 py-2 border">{{ $controle->actie_nodig ? 'Ja' : 'Nee' }}</td>
                                            <td class="px-4 py-2 border">{{ $controle->user->name }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @else
                            <p>Geen controles geregistreerd.</p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="mt-6 flex space-x-4">
                <a href="{{ route('aeds.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                    Terug naar lijst
                </a>
                @if($aed->status === 'actief' || $aed->status === 'archief')
                    <a href="{{ route('aeds.edit', $aed) }}" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                        Bewerken
                    </a>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>

