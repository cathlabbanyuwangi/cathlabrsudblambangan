<x-guest-layout>
    <div class="w-full sm:max-w-md mt-6 px-8 py-10 bg-white/90 backdrop-blur-xl shadow-[0_12px_40px_rgba(148,163,184,0.1)] border border-sky-100/60 rounded-3xl overflow-hidden sm:mx-auto">
        
        <div class="mb-8 text-center">
            <h2 class="text-2xl font-bold text-slate-800 tracking-tight">{{ __('Welcome Back') }}</h2>
            <p class="text-sm text-slate-500 mt-1.5">{{ __('Please enter your credentials to access your account.') }}</p>
        </div>

        <x-auth-session-status class="mb-4" :status="session('status')" />

        <form method="POST" action="{{ route('login') }}" class="space-y-5">
            @csrf

            <div>
                <label for="username" class="block text-xs font-semibold uppercase tracking-wider text-slate-600 mb-2">
                    {{ __('Username') }}
                </label>
                <div class="relative">
                    <input id="username" type="text" name="username" value="{{ old('username') }}" required autofocus autocomplete="username"
                        class="block w-full rounded-xl border border-slate-200 py-3 px-4 text-slate-800 shadow-2xs placeholder:text-slate-400 focus:border-sky-400 focus:ring-2 focus:ring-sky-100 sm:text-sm transition-all duration-200 bg-slate-50/50 hover:bg-white focus:bg-white"
                        placeholder="Enter your username">
                </div>
                <x-input-error :messages="$errors->get('username')" class="mt-2" />
            </div>

            <div>
                <div class="flex items-center justify-between mb-2">
                    <label for="password" class="block text-xs font-semibold uppercase tracking-wider text-slate-600">
                        {{ __('Password') }}
                    </label>
                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="text-xs font-medium text-sky-600 hover:text-sky-700 transition duration-150">
                            {{ __('Forgot password?') }}
                        </a>
                    @endif
                </div>
                <div class="relative">
                    <input id="password" type="password" name="password" required autocomplete="current-password"
                        class="block w-full rounded-xl border border-slate-200 py-3 px-4 text-slate-800 shadow-2xs placeholder:text-slate-400 focus:border-sky-400 focus:ring-2 focus:ring-sky-100 sm:text-sm transition-all duration-200 bg-slate-50/50 hover:bg-white focus:bg-white"
                        placeholder="••••••••">
                </div>
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <div class="flex items-center pt-1">
                <label for="remember_me" class="flex items-center gap-3 cursor-pointer group">
                    <input id="remember_me" type="checkbox" name="remember" 
                        class="h-4 w-4 rounded border-slate-300 text-sky-600 shadow-2xs focus:ring-sky-400 focus:ring-offset-0 transition-all cursor-pointer">
                    <span class="text-sm font-medium text-slate-600 group-hover:text-slate-800 transition-colors">{{ __('Keep me logged in') }}</span>
                </label>
            </div>

            <div class="pt-3">
                <button type="submit" 
                    class="flex w-full justify-center items-center gap-2 rounded-xl bg-sky-600 px-4 py-3.5 text-sm font-semibold text-white shadow-sm hover:bg-sky-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-sky-400 transition-all duration-200 ease-in-out">
                    {{ __('Sign In to Dashboard') }}
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
                    </svg>
                </button>
            </div>
        </form>
    </div>
</x-guest-layout>