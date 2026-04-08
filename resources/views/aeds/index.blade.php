<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Mijn AEDs') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Create button -->
            <div class="mb-6">
                <a href="{{ route('aeds.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    AED toevoegen
                </a>
            </div>

            <!-- Search -->
            <form method="GET">
                <div class="mb-6">
                    <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Zoek op serienummer, adres..." class="border rounded py-2 px-4 w-full md:w-1/3">
                    <button type="submit" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded mt-2">Zoeken</button>
                </div>
            </form>

            <!-- AEDs table -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    @if($aeds->count() > 0)
                        <table class="min-w-full table-auto">
                            <thead>
                                <tr>
                                    <th class="px-4 py-2">Type</th>
                                    <th class="px-4 py-2">Serienummer</th>
                                    <th class="px-4 py-2">Adres</th>
                                    <th class="px-4 py-2">Status</th>
                                    <th class="px-4 py-2">Controles</th>
                                    <th class="px-4 py-2">Aandacht nodig</th>
                                    <th class="px-4 py-2">Acties</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($aeds as $aed)
                                    <tr class="border-b {{ $aed->trashed() ? 'bg-gray-100 text-gray-500' : '' }} {{ $aed->needsAttention() ? 'bg-yellow-100' : '' }}">
                                        <td class="px-4 py-2">{{ $aed->type }}</td>
                                        <td class="px-4 py-2">{{ $aed->serienummer ?? '-' }}</td>
                                        <td class="px-4 py-2">{{ $aed->adres }} {{ $aed->huisnummer }}</td>
                                        <td class="px-4 py-2">
                                            <span class="px-2 py-1 rounded text-xs font-bold {{ $aed->status === 'actief' ? 'bg-green-200 text-green-800' : ($aed->status === 'archief' ? 'bg-yellow-200 text-yellow-800' : 'bg-red-200 text-red-800') }}">
                                                {{ ucfirst($aed->status) }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-2">{{ $aed->controles_count }}</td>
                                        <td class="px-4 py-2">
                                            @if($aed->needsAttention())
                                                <span class="text-red-600">Ja</span>
                                            @else
                                                <span class="text-green-600">Nee</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-2">
                                            <a href="{{ route('aeds.show', $aed) }}" class="text-blue-500 hover:underline mr-2">Bekijk</a>
                                            @if($aed->status === 'actief')
                                                <form action="{{ route('aeds.archive', $aed) }}" method="POST" class="inline" onsubmit="return confirm('AED naar archief verplaatsen? Geschiedenis blijft bewaard.');">
                                                    @csrf
                                                    @method('PUT')
                                                    <button type="submit" class="text-yellow-600 hover:underline mr-2">Archiveren</button>
                                                </form>
                                            @endif
                                            @if($aed->status === 'actief' || $aed->status === 'archief')
                                                <a href="{{ route('aeds.edit', $aed) }}" class="text-green-500 hover:underline mr-2">Bewerken</a>
                                            @endif
                                            @if($aed->status === 'archief')
                                                <form action="{{ route('aeds.destroy', $aed) }}" method="POST" class="inline" onsubmit="return confirm('AED permanent verwijderen?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-600 hover:underline">Verwijderen</button>
                                                </form>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <p>Geen AEDs gevonden. <a href="{{ route('aeds.create') }}" class="text-blue-500 hover:underline">Voeg de eerste toe</a>.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

