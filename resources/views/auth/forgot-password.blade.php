<x-guest-layout>
    <div class="mb-4 text-sm text-gray-600">
        {{ __('Wachtwoord vergeten? Geen probleem. Laat ons je e-mailadres weten en we sturen je een link om je wachtwoord te herstellen, waarmee je een nieuw wachtwoord kunt kiezen.') }}
    </div>

    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}">

        @csrf

        <div>
            <x-input-label for="email" :value="__('E-mailadres')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="flex items-center justify-between mt-4 gap-3">
            <a
                href="{{ route('login') }}"
                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                {{ __('Terug naar login') }}
            </a>

            <x-primary-button>
                {{ __('Stuur wachtwoord herstellink') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
