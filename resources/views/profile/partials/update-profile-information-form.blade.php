<section class="space-y-6">
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Profielinformatie') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __("Werk de profielgegevens en het e-mailadres van je account bij.") }}
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('patch')

        <div>
            <x-input-label for="name" :value="__('Naam')" />
            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full"
                :value="old('name', $user->name)" required autofocus autocomplete="name" />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <div>
            <x-input-label for="email" :value="__('E-mailadres')" />
            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full"
                :value="old('email', $user->email)" required autocomplete="username" />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div>
                    <p class="text-sm mt-2 text-gray-800">
                        {{ __('Je e-mailadres is nog niet geverifieerd.') }}

                        <button form="send-verification"
                            class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            {{ __('Klik hier om de verificatie-e-mail opnieuw te verzenden.') }}
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 font-medium text-sm text-green-600">
                            {{ __('Er is een nieuwe verificatielink naar je e-mailadres verzonden.') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Opslaan') }}</x-primary-button>

            @if (session('status') === 'profile-updated')
                <p x-data="{ show: true }" x-show="show" x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-gray-600">
                    {{ __('Opgeslagen.') }}
                </p>
            @endif
        </div>
    </form>

    {{-- ========================= --}}
    {{-- 2FA SECTION               --}}
    {{-- ========================= --}}

    <div class="mt-10 border-t pt-6">
        <h2 class="text-lg font-medium text-gray-900">Twee-factor authenticatie</h2>

        @if (! auth()->user()->two_factor_enabled)
            <p class="text-sm text-gray-600 mt-1">
                2FA staat UIT
            </p>

            @if (auth()->user()->two_factor_secret)
                <div class="mt-4 space-y-4">
                    <p class="text-sm text-gray-600">
                        Scan deze code met je Authenticator App:
                    </p>

                    <div class="mt-2 inline-block bg-white p-2 rounded border">
                        @php
                            $company = config('app.name', 'Laravel');
                            $challengeUrl = "otpauth://totp/" . rawurlencode($company) . ":" . rawurlencode(auth()->user()->email) . "?secret=" . auth()->user()->two_factor_secret . "&issuer=" . rawurlencode($company);
                            
                            // Gecorrigeerde namespaces voor BaconQrCode v3
                            $renderer = new \BaconQrCode\Renderer\ImageRenderer(
                                new \BaconQrCode\Renderer\RendererStyle\RendererStyle(200),
                                new \BaconQrCode\Renderer\Image\SvgImageBackEnd()
                            );
                            $writer = new \BaconQrCode\Writer($renderer);
                            $qrCodeSvg = $writer->writeString($challengeUrl);
                        @endphp
                        
                        {!! $qrCodeSvg !!}
                    </div>

                    <p class="text-sm text-gray-600">
                        Sleutel: <code class="font-mono bg-gray-100 px-1 rounded">{{ auth()->user()->two_factor_secret }}</code>
                    </p>

                    <form method="POST" action="{{ route('2fa.confirm') }}" class="mt-4 space-y-2">
                        @csrf

                        <input type="text" name="code" placeholder="6-cijferige code"
                            class="border rounded px-3 py-2 w-full max-w-xs block" required autocomplete="one-time-code">

                        @error('code')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror

                        <button type="submit"
                            class="bg-blue-600 text-white px-4 py-2 rounded block">
                            Code bevestigen
                        </button>
                    </form>

                    <form method="POST" action="{{ route('2fa.disable') }}" class="mt-2">
                        @csrf
                        <button type="submit" class="text-sm text-red-600 underline">
                            Annuleren
                        </button>
                    </form>
                </div>
            @else
                <form method="POST" action="{{ route('2fa.enable') }}" class="mt-4">
                    @csrf
                    <button type="submit"
                        class="bg-green-600 text-white px-4 py-2 rounded">
                        2FA inschakelen
                    </button>
                </form>
            @endif
        @else
            <p class="text-sm text-green-600 mt-1">
                ✅ 2FA staat AAN
            </p>

            @if (session('status'))
                <p class="text-sm text-green-600 mt-2">{{ session('status') }}</p>
            @endif

            <form method="POST" action="{{ route('2fa.disable') }}" class="mt-4">
                @csrf
                <button type="submit"
                    class="bg-red-600 text-white px-4 py-2 rounded">
                    2FA uitschakelen
                </button>
            </form>
        @endif
    </div>
</section>
