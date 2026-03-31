<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-lg md:text-xl text-gray-800 leading-tight">
            {{ __('Gebruikers Beheer') }}
        </h2>
    </x-slot>

    <div class="py-6 md:py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            @if (session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4 text-sm">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4 text-sm">
                    {{ session('error') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-8">
                <div class="p-4 md:p-6 text-gray-900">

                    <h3 class="text-base md:text-lg font-bold mb-4">
                        Alle Gebruikers ({{ $users->count() }})
                    </h3>

                    <div class="mb-6">
                        <div class="flex gap-2">
                            <input type="text" id="searchInput" value="{{ $search ?? '' }}" placeholder="Zoek op naam of email... (live)" class="flex-1 px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500" oninput="filterUsers(this.value)">
                            <a href="{{ route('admin.users.index') }}" class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-md whitespace-nowrap">Reset</a>
                        </div>
                    </div>

                    <script>
                        function filterUsers(value) {
                            const rows = document.querySelectorAll('tbody tr');
                            const count = document.querySelector('h3');
                            let visibleCount = 0;
                            rows.forEach(row => {
                                const text = row.textContent.toLowerCase();
                                if (text.includes(value.toLowerCase()) || value === '') {
                                    row.style.display = '';
                                    visibleCount++;
                                } else {
                                    row.style.display = 'none';
                                }
                            });
                            count.textContent = `Gevonden Gebruikers (${visibleCount})`;
                        }
                    </script>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 text-xs md:text-sm">  

                            <thead class="bg-gray-50">
                            <tr>
                                <th class="hidden md:table-cell px-4 md:px-6 py-2 md:py-3 text-left font-medium text-gray-500 uppercase">
                                    ID
                                </th>

                                <th class="px-4 md:px-6 py-2 md:py-3 text-left font-medium text-gray-500 uppercase">
                                    Naam
                                </th>

                                <th class="hidden sm:table-cell px-4 md:px-6 py-2 md:py-3 text-left font-medium text-gray-500 uppercase">
                                    Email
                                </th>

                                <th class="px-4 md:px-6 py-2 md:py-3 text-left font-medium text-gray-500 uppercase">
                                    Admin
                                </th>

                                <th class="px-4 md:px-6 py-2 md:py-3 text-left font-medium text-gray-500 uppercase">
                                    Acties
                                </th>
                            </tr>
                            </thead>

                            <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($users as $user)
                                <tr>

                                    <td class="hidden md:table-cell px-4 md:px-6 py-3 whitespace-nowrap">
                                        {{ $user->id }}
                                    </td>

                                    <td class="px-4 md:px-6 py-3 whitespace-nowrap font-medium">
                                        <a href="{{ route('admin.users.show', $user) }}" class="text-blue-600 hover:underline">
                                            {{ $user->name }}
                                        </a>
                                    </td>

                                    <td class="hidden sm:table-cell px-4 md:px-6 py-3 whitespace-nowrap">
                                        {{ $user->email }}
                                    </td>

                                    <td class="px-4 md:px-6 py-3 whitespace-nowrap">
                                            <span class="px-2 py-1 inline-flex text-xs font-semibold rounded-full
                                                {{ $user->is_admin ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                                {{ $user->is_admin ? 'Ja' : 'Nee' }}
                                            </span>
                                    </td>

                                    <td class="px-4 md:px-6 py-3 whitespace-nowrap">
                                        @if($user->id !== auth()->id())

                                            <div class="flex flex-col md:flex-row gap-2 md:gap-4">

                                                <a href="{{ route('admin.users.edit', $user) }}"
                                                   class="text-blue-600 hover:text-blue-900 font-medium">
                                                    Bewerken
                                                </a>

                                                <form action="{{ route('admin.users.destroy', $user) }}"
                                                      method="POST"
                                                      onsubmit="return confirm('Weet je zeker dat je {{ $user->name }} wilt verwijderen?')">
                                                    @csrf
                                                    @method('DELETE')

                                                    <button type="submit"
                                                            class="text-red-600 hover:text-red-900 text-left">
                                                        Verwijder
                                                    </button>
                                                </form>

                                            </div>

                                        @else
                                            <span class="text-gray-500">Zelf</span>
                                        @endif
                                    </td>

                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                                        Geen gebruikers gevonden.
                                    </td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-6">
                        <x-primary-button
                            onclick="window.location.href='{{ route('admin.users.create') }}'"
                            class="w-full md:w-auto bg-green-600 hover:bg-green-700 focus:bg-green-700 active:bg-green-800">
                            {{ __('Nieuwe Gebruiker Toevoegen') }}
                        </x-primary-button>
                    </div>

                </div>
            </div>

        </div>
    </div>
</x-app-layout>
