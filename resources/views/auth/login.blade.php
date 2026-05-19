@extends('layouts.app')

@section('content')
{{-- Fond Gradient Bleu Royal & Violet --}}
<div class="min-h-screen flex items-center justify-center py-8 bg-gradient-to-br from-[#1E40AF] via-[#4C1D95] to-[#1E3A8A] relative overflow-hidden">
    
    <!-- Éléments décoratifs d'ambiance (Orbes lumineux) -->
    <div class="absolute top-10 left-10 w-72 h-72 bg-amber-500/20 rounded-full blur-3xl animate-pulse"></div>
    <div class="absolute bottom-10 right-10 w-80 h-80 bg-violet-500/30 rounded-full blur-3xl"></div>

    <div class="w-full max-w-md px-4 relative z-10" data-aos="zoom-in">
        <!-- En-tête -->
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-white mb-2 drop-shadow-lg">Bon retour 👋</h1>
            <p class="text-sm text-amber-200/90 font-medium tracking-wide">Connectez-vous à votre espace</p>
        </div>

        <!-- Carte Formulaire : EFFET VERRE PREMIUM (Glassmorphism) -->
        <div class="bg-white/10 backdrop-blur-xl border border-white/20 shadow-2xl rounded-2xl p-8" x-data="{ showPassword: false }">
            <form method="POST" action="{{ route('login') }}" class="space-y-6">
                @csrf

                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-200 mb-2">Adresse email</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" 
                           class="w-full px-4 py-3 rounded-lg bg-white/5 border border-white/20 text-white placeholder-gray-400 focus:outline-none focus:border-amber-400 focus:ring-2 focus:ring-amber-400/30 transition-all" 
                           placeholder="vous@exemple.com" required autofocus autocomplete="email">
                    @error('email') <p class="text-xs text-rose-400 mt-2">{{ $message }}</p> @enderror
                </div>

                <!-- Mot de passe -->
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-200 mb-2">Mot de passe</label>
                    <div class="relative">
                        <input id="password" :type="showPassword ? 'text' : 'password'" name="password" 
                               class="w-full px-4 py-3 rounded-lg bg-white/5 border border-white/20 text-white placeholder-gray-400 focus:outline-none focus:border-amber-400 focus:ring-2 focus:ring-amber-400/30 transition-all pr-10" 
                               placeholder="••••••••" required autocomplete="current-password">
                        
                        <button type="button" @click="showPassword = !showPassword"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-amber-400 transition p-1" tabindex="-1">
                            <svg x-show="!showPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            <svg x-show="showPassword" x-cloak class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                            </svg>
                        </button>
                    </div>
                    @error('password') <p class="text-xs text-rose-400 mt-2">{{ $message }}</p> @enderror
                </div>

                <!-- Options -->
                <div class="flex items-center justify-between text-sm">
                    <label class="flex items-center gap-2 cursor-pointer select-none">
                        <input type="checkbox" name="remember" class="rounded border-gray-500 text-amber-500 focus:ring-amber-500 bg-white/10">
                        <span class="text-gray-300">Se souvenir de moi</span>
                    </label>
                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="text-amber-400 hover:text-amber-300 font-medium transition">Mot de passe oublié ?</a>
                    @endif
                </div>

                <!-- Bouton Connexion (Doré Premium) -->
                <button type="submit" class="w-full py-3.5 bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-400 hover:to-amber-500 text-white font-bold rounded-lg shadow-lg shadow-amber-500/30 transition-all transform hover:-translate-y-0.5 hover:shadow-xl">
                    Se connecter
                </button>
            </form>

            <p class="text-center text-sm text-gray-400 mt-8">
                Pas encore de compte ? 
                <a href="{{ route('register') }}" class="text-amber-400 font-semibold hover:text-amber-300 transition underline decoration-amber-400/30 underline-offset-4">Créer un compte</a>
            </p>
        </div>

        <div class="text-center mt-8">
            <a href="{{ url('/') }}" class="text-sm text-gray-400 hover:text-white transition flex items-center justify-center gap-2 group">
                <svg class="w-4 h-4 transform group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Retour à l'accueil
            </a>
        </div>
    </div>
</div>
@endsection