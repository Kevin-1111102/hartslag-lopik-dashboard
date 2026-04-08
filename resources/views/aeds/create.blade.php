<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Nieuwe AED toevoegen
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <form method="POST" action="{{ route('aeds.store') }}">
                @csrf
                
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <x-input-label for="type" :value="__('Type')" />
                            <x-text-input id="type" class="block mt-1 w-full" type="text" name="type" :value="old('type')" required autofocus />
                            <x-input-error :messages="$errors->get('type')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="serienummer" :value="__('Serienummer')" />
                            <x-text-input id="serienummer" class="block mt-1 w-full" type="text" name="serienummer" :value="old('serienummer')" />
                            <x-input-error :messages="$errors->get('serienummer')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="adres" :value="__('Adres')" />
                            <x-text-input id="adres" class="block mt-1 w-full" type="text" name="adres" :value="old('adres')" required />
                            <x-input-error :messages="$errors->get('adres')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="huisnummer" :value="__('Huisnummer')" />
                            <x-text-input id="huisnummer" class="block mt-1 w-full" type="text" name="huisnummer" :value="old('huisnummer')" />
                            <x-input-error :messages="$errors->get('huisnummer')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="plaats" :value="__('Plaats')" />
                            <x-text-input id="plaats" class="block mt-1 w-full" type="text" name="plaats" :value="old('plaats')" required />
                            <x-input-error :messages="$errors->get('plaats')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="beschrijving" :value="__('Beschrijving')" />
                            <textarea id="beschrijving" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm" name="beschrijving">{{ old('beschrijving') }}</textarea>
                            <x-input-error :messages="$errors->get('beschrijving')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="pincode" :value="__('Pincode')" />
                            <x-text-input id="pincode" class="block mt-1 w-full" type="text" name="pincode" :value="old('pincode')" />
                            <x-input-error :messages="$errors->get('pincode')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="onderhoudscode" :value="__('Onderhoudscode')" />
                            <x-text-input id="onderhoudscode" class="block mt-1 w-full" type="text" name="onderhoudscode" :value="old('onderhoudscode')" />
                            <x-input-error :messages="$errors->get('onderhoudscode')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="batterij_vervaldatum" :value="__('Batterij vervaldatum')" />
                            <x-text-input id="batterij_vervaldatum" class="block mt-1 w-full" type="date" name="batterij_vervaldatum" :value="old('batterij_vervaldatum')" />
                            <x-input-error :messages="$errors->get('batterij_vervaldatum')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="elektroden_vervaldatum" :value="__('Elektroden vervaldatum')" />
                            <x-text-input id="elektroden_vervaldatum" class="block mt-1 w-full" type="date" name="elektroden_vervaldatum" :value="old('elektroden_vervaldatum')" />
                            <x-input-error :messages="$errors->get('elektroden_vervaldatum')" class="mt-2" />
                        </div>
                    </div>

                    <!-- Booleans -->
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-8">
                        <div>
                            <label class="flex items-center">
                                <input type="checkbox" name="shl_beheerder" value="1" {{ old('shl_beheerder') ? 'checked' : '' }} class="rounded border-gray-300 text-blue-600">
                                <span class="ml-2">SHL beheerder</span>
                            </label>
                        </div>
                        <div>
                            <label class="flex items-center">
                                <input type="checkbox" name="shl_verantwoordelijk_controle" value="1" {{ old('shl_verantwoordelijk_controle') ? 'checked' : '' }} class="rounded border-gray-300 text-blue-600">
                                <span class="ml-2">SHL controle</span>
                            </label>
                        </div>
                        <div>
                            <label class="flex items-center">
                                <input type="checkbox" name="shl_hartslagnu_beheer" value="1" {{ old('shl_hartslagnu_beheer') ? 'checked' : '' }} class="rounded border-gray-300 text-blue-600">
                                <span class="ml-2">HartslagNu beheer</span>
                            </label>
                        </div>
                        <div>
                            <label class="flex items-center">
                                <input type="checkbox" name="externe_onderhoud" value="1" {{ old('externe_onderhoud') ? 'checked' : '' }} class="rounded border-gray-300 text-blue-600">
                                <span class="ml-2">Externe onderhoud</span>
                            </label>
                        </div>
                    </div>

                    <div class="flex items-center justify-end mt-8">
                        <a href="{{ route('aeds.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded mr-4">
                            Annuleren
                        </a>
                        <x-primary-button>
                            {{ __('AED toevoegen') }}
                        </x-primary-button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>

