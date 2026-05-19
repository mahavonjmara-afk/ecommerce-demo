@extends('layouts.app')

@section('content')
<div class="min-h-screen flex items-center justify-center py-8 bg-gradient-to-br from-[#1E40AF] via-[#4C1D95] to-[#1E3A8A] relative overflow-hidden">
    
    <!-- Éléments décoratifs d'ambiance (identiques aux autres pages) -->
    <div class="absolute top-10 left-10 w-72 h-72 bg-amber-500/20 rounded-full blur-3xl animate-pulse"></div>
    <div class="absolute bottom-10 right-10 w-80 h-80 bg-violet-500/30 rounded-full blur-3xl"></div>

    <div class="w-full max-w-md px-4 relative z-10" data-aos="zoom-in">
        <!-- En-tête -->
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-white mb-2 drop-shadow-lg">Mot de passe oublié ? 🔑</h1>
            <p class="text-sm text-amber-200/90 font-medium tracking-wide">Entrez votre email, nous vous enverrons un lien de réinitialisation</p>
        </div>

        <!-- Carte Formulaire : Glassmorphism Premium -->
        <div class="bg-white/10 backdrop-blur-xl border border-white/20 shadow-2xl rounded-2xl p-8">
            
            {{-- Message de succès (standard Laravel Breeze) --}}
            @if (session('status'))
                <div class="mb-6 p-4 rounded-lg bg-emerald-500/20 border border-emerald-400/30 text-emerald-200 text-sm font-medium text-center animate-fade-in">
                    {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ route('password.email') }}" class="space-y-6">
                @csrf

                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-200 mb-2">Adresse email</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" 
                           class="w-full px-4 py-3 rounded-lg bg-white/5 border border-white/20 text-white placeholder-gray-400 focus:outline-none focus:border-amber-400 focus:ring-2 focus:ring-amber-400/30 transition-all" 
                           placeholder="vous@exemple.com" required autofocus autocomplete="email">
                    @error('email') <p class="text-xs text-rose-400 mt-2">{{ $message }}</p> @enderror
                </div>

                <!-- Bouton Envoyer -->
                <button type="submit" class="w-full py-3.5 bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-400 hover:to-amber-500 text-white font-bold rounded-lg shadow-lg shadow-amber-500/30 transition-all transform hover:-translate-y-0.5 hover:shadow-xl">
                    Envoyer le lien de réinitialisation
                </button>
            </form>

            <!-- Lien retour connexion -->
            <div class="text-center mt-8 pt-6 border-t border-white/10">
                <a href="{{ route('login') }}" class="text-sm text-amber-400 font-semibold hover:text-amber-300 transition flex items-center justify-center gap-2 group">
                    <svg class="w-4 h-4 transform group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Retour à la connexion
                </a>
            </div>
        </div>
    </div>
</div>
@endsection