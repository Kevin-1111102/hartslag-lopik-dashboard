<x-guest-layout>
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <h2 class="text-xl font-semibold text-gray-900">Tweestapsverificatie vereist</h2>
    <p class="mt-2 text-sm text-gray-600">Open je authenticator app en voer de code in.</p>

    <form method="POST" action="{{ route('2fa.login') }}" class="mt-4">
        @csrf

        <div>
            <x-input-label for="code" :value="__('Code')" />


            <x-text-input
                id="code"
                class="block mt-1 w-full"
                type="text"
                name="code"
                required
                autofocus
                inputmode="numeric"
                autocomplete="one-time-code"
                placeholder="123456"
            />

            <x-input-error :messages="$errors->get('code')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <x-primary-button>
                {{ __('Inloggen') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>

