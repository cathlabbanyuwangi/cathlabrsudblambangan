<x-guest-layout>
    <!-- Card Container Mewah -->
    <div class="w-full sm:max-w-md mt-6 px-8 py-10 bg-white shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-gray-100 rounded-3xl overflow-hidden sm:mx-auto">
        
        <!-- Header Text -->
        <div class="mb-10 text-center">
            <h2 class="text-3xl font-extrabold text-gray-900 tracking-tight">{{ __('Welcome Back') }}</h2>
            <p class="text-sm text-gray-500 mt-2">{{ __('Please enter your credentials to access your account.') }}</p>
        </div>

        <!-- Session Status -->
        <x-auth-session-status class="mb-4" :status="session('status')" />

        <form method="POST" action="{{ route('login') }}" class="space-y-6">
            @csrf

            <!-- Username Input -->
            <div>
                <label for="username" class="block text-sm font-semibold text-gray-700 mb-2">
                    {{ __('Username') }}
                </label>
                <div class="relative">
                    <input id="username" type="text" name="username" value="{{ old('username') }}" required autofocus autocomplete="username"
                        class="block w-full rounded-xl border-0 py-3.5 px-4 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-200 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6 transition-all duration-200 ease-in-out bg-gray-50 hover:bg-white focus:bg-white"
                        placeholder="Enter your username">
                </div>
                <x-input-error :messages="$errors->get('username')" class="mt-2" />
            </div>

            <!-- Password Input -->
            <div>
                <div class="flex items-center justify-between mb-2">
                    <label for="password" class="block text-sm font-semibold text-gray-700">
                        {{ __('Password') }}
                    </label>
                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-800 transition duration-150 ease-in-out">
                            {{ __('Forgot password?') }}
                        </a>
                    @endif
                </div>
                <div class="relative">
                    <input id="password" type="password" name="password" required autocomplete="current-password"
                        class="block w-full rounded-xl border-0 py-3.5 px-4 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-200 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6 transition-all duration-200 ease-in-out bg-gray-50 hover:bg-white focus:bg-white"
                        placeholder="••••••••">
                </div>
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <!-- Remember Me -->
            <div class="flex items-center mt-2">
                <label for="remember_me" class="flex items-center gap-3 cursor-pointer group">
                    <div class="relative flex items-center justify-center">
                        <input id="remember_me" type="checkbox" name="remember" 
                            class="peer h-5 w-5 rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-600 focus:ring-offset-0 transition-all duration-200 ease-in-out cursor-pointer">
                    </div>
                    <span class="text-sm font-medium text-gray-600 group-hover:text-gray-900 transition-colors duration-200">{{ __('Keep me logged in') }}</span>
                </label>
            </div>

            <!-- Submit Button -->
            <div class="pt-2">
                <button type="submit" 
                    class="flex w-full justify-center items-center gap-2 rounded-xl bg-gray-900 px-4 py-4 text-sm font-bold text-white shadow-md hover:bg-indigo-600 hover:shadow-lg focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 transition-all duration-300 ease-in-out transform hover:-translate-y-0.5">
                    {{ __('Sign In to Dashboard') }}
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
                    </svg>
                </button>
            </div>
        </form>
    </div>
</x-guest-layout>