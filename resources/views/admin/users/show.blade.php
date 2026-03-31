<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-lg md:text-xl text-gray-800 leading-tight">
            Gebruiker Details
        </h2>
    </x-slot>

    <div class="py-6 md:py-12">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">

            <div class="bg-white shadow-sm sm:rounded-lg p-6">

                <!-- Info -->
                <div class="space-y-4 text-sm md:text-base">

                    <div>
                        <strong>ID:</strong> {{ $user->id }}
                    </div>

                    <div>
                        <strong>Naam:</strong> {{ $user->name }}
                    </div>

                    <div>
                        <strong>Email:</strong> {{ $user->email }}
                    </div>

                    <div>
                        <strong>Admin:</strong>
                        <span class="ml-2 px-2 py-1 text-xs rounded-full
                            {{ $user->is_admin ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                            {{ $user->is_admin ? 'Ja' : 'Nee' }}
                        </span>
                    </div>

                    <div>
                        <strong>Aangemaakt op:</strong> {{ $user->created_at }}
                    </div>

                </div>

                <!-- Acties -->
                <div class="mt-8 flex flex-col md:flex-row gap-3">

                    <!-- Terug -->
                    <x-secondary-button onclick="window.location.href='{{ route('admin.users.index') }}'">
                        Terug
                    </x-secondary-button>

                    <!-- Bewerken -->
                    <x-primary-button onclick="window.location.href='{{ route('admin.users.edit', $user) }}'">
                        Bewerken
                    </x-primary-button>

                    <!-- Verwijderen -->
                    @if($user->id !== auth()->id())
                        <x-danger-button onclick="document.getElementById('delete-form-{{ $user->id }}').submit()">
                            Verwijder
                        </x-danger-button>
                        <form id="delete-form-{{ $user->id }}" action="{{ route('admin.users.destroy', $user) }}" method="POST" style="display: none;">
                            @csrf
                            @method('DELETE')
                        </form>
                    @endif
                </div>

            </div>

        </div>
    </div>
</x-app-layout>
