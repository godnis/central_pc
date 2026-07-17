<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <h1 class="font-display text-2xl font-bold tracking-tight text-gray-900 dark:text-gray-100">{{ __('Entrar') }}</h1>
    <p class="mt-1 text-sm text-gray-500">{{ __('Acesse o inventário com a sua conta.') }}</p>

    <form method="POST" action="{{ route('login') }}" class="mt-8">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('E-mail')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-5">
            <x-input-label for="password" :value="__('Senha')" />

            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="current-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="block mt-5">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-brand-600 shadow-sm focus:ring-brand-500" name="remember">
                <span class="ms-2 text-sm text-gray-600">{{ __('Manter conectado') }}</span>
            </label>
        </div>

        <div class="mt-8">
            <x-primary-button class="w-full">
                {{ __('Entrar') }}
            </x-primary-button>
        </div>

        @if (Route::has('password.request'))
            <p class="mt-4 text-center">
                <a class="text-sm text-gray-500 hover:text-brand-600 hover:underline rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-500" href="{{ route('password.request') }}">
                    {{ __('Esqueceu a senha?') }}
                </a>
            </p>
        @endif
    </form>
</x-guest-layout>
