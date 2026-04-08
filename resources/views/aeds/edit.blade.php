<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            AED bewerken - {{ $aed->serienummer ?? 'Onbekend' }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <form method="POST" action="{{ route('aeds.update', $aed) }}">
                @csrf
                @method('PUT')
                
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <!-- Same form as create, but with old values from $aed -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <x-input-label for="type" :value="__('Type')" />
                            <x-text-input id="type" class="block mt-1 w-full" type="text" name="type" value="{{ $aed->type }}" required autofocus />
                            <x-input-error :messages="$errors->get('type')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="serienummer" :value="__('Serienummer')" />
                            <x-text-input id="serienummer" class="block mt-1 w-full" type="text" name="serienummer" value="{{ $aed->serienummer }}" />
                            <x-input-error :messages="$errors->get('serienummer')" class="mt-2" />
                        </div>

                        <!-- Copy all fields like create.blade.php, replacing :value="old('field')" with value="{{ $aed->field }}" -->
                        <div>
                            <x-input-label for="adres" :value="__('Adres')" />
                            <x-text-input id="adres" class="block mt-1 w-full" type="text" name="adres" value="{{ $aed->adres }}" required />
                            <x-input-error :messages="$errors->get('adres')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="huisnummer" :value="__('Huisnummer')" />
                            <x-text-input id="huisnummer" class="block mt-1 w-full" type="text" name="huisnummer" value="{{ $aed->huisnummer }}" />
                            <x-input-error :messages="$errors->get('huisnummer')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="plaats" :value="__('Plaats')" />
                            <x-text-input id="plaats" class="block mt-1 w-full" type="text" name="plaats" value="{{ $aed->plaats }}" required />
                            <x-input-error :messages="$errors->get('plaats')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="beschrijving" :value="__('Beschrijving')" />
                            <textarea id="beschrijving" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm" name="beschrijving">{{ $aed->beschrijving }}</textarea>
                            <x-input-error :messages="$errors->get('beschrijving')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="pincode" :value="__('Pincode')" />
                            <x-text-input id="pincode" class="block mt-1 w-full" type="text" name="pincode" value="{{ $aed->pincode }}" />
                            <x-input-error :messages="$errors->get('pincode')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="onderhoudscode" :value="__('Onderhoudscode')" />
                            <x-text-input id="onderhoudscode" class="block mt-1 w-full" type="text" name="onderhoudscode" value="{{ $aed->onderhoudscode }}" />
                            <x-input-error :messages="$errors->get('onderhoudscode')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="batterij_vervaldatum" :value="__('Batterij vervaldatum')" />
                            <x-text-input id="batterij_vervaldatum" class="block mt-1 w-full" type="date" name="batterij_vervaldatum" value="{{ $aed->batterij_vervaldatum?->format('Y-m-d') }}" />
                            <x-input-error :messages="$errors->get('batterij_vervaldatum')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="elektroden_vervaldatum" :value="__('Elektroden vervaldatum')" />
                            <x-text-input id="elektroden_vervaldatum" class="block mt-1 w-full" type="date" name="elektroden_vervaldatum" value="{{ $aed->elektroden_vervaldatum?->format('Y-m-d') }}" />
                            <x-input-error :messages="$errors->get('elektroden_vervaldatum')" class="mt-2" />
                        </div>
                    </div>

                    <!-- Booleans -->
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-8">
                        <div>
                            <label class="flex items-center">
                                <input type="checkbox" name="shl_beheerder" value="1" {{ $aed->shl_beheerder ? 'checked' : '' }} class="rounded border-gray-300 text-blue-600">
                                <span class="ml-2">SHL beheerder</span>
                            </label>
                        </div>
                        <div>
                            <label class="flex items-center">
                                <input type="checkbox" name="shl_verantwoordelijk_controle" value="1" {{ $aed->shl_verantwoordelijk_controle ? 'checked' : '' }} class="rounded border-gray-300 text-blue-600">
                                <span class="ml-2">SHL controle</span>
                            </label>
                        </div>
                        <div>
                            <label class="flex items-center">
                                <input type="checkbox" name="shl_hartslagnu_beheer" value="1" {{ $aed->shl_hartslagnu_beheer ? 'checked' : '' }} class="rounded border-gray-300 text-blue-600">
                                <span class="ml-2">HartslagNu beheer</span>
                            </label>
                        </div>
                        <div>
                            <label class="flex items-center">
                                <input type="checkbox" name="externe_onderhoud" value="1" {{ $aed->externe_onderhoud ? 'checked' : '' }} class="rounded border-gray-300 text-blue-600">
                                <span class="ml-2">Externe onderhoud</span>
                            </label>
                        </div>
                    </div>

                    <div class="flex items-center justify-end mt-8">
                        <a href="{{ route('aeds.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded mr-4">
                            Annuleren
                        </a>
                        <x-primary-button>
                            {{ __('Opslaan') }}
                        </x-primary-button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>

