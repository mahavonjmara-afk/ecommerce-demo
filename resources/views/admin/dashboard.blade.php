@extends('layouts.app')

@section('content')
<div class="py-8 md:py-12 bg-background/50 dark:bg-slate-950/50 min-h-screen transition-colors duration-300">
    <div class="container mx-auto px-4 max-w-7xl">
        
        {{-- En-tête Dashboard --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-8 gap-4" data-aos="fade-down">
            <div>
                <h1 class="text-2xl md:text-3xl font-bold text-secondary dark:text-slate-100 mb-1">🎛️ Panel Administrateur</h1>
                <p class="text-sm text-secondary/60 dark:text-slate-400">Bienvenue, {{ auth()->user()->name }}. Gérez votre boutique en toute simplicité.</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('admin.orders.index') }}" class="btn-primary px-4 py-2 text-sm flex items-center gap-2 bg-primary hover:bg-primaryLight dark:bg-blue-600 dark:hover:bg-blue-500">
                    📦 Voir les commandes
                </a>
                <a href="{{ url('/produits') }}" class="px-4 py-2 text-sm font-medium text-secondary dark:text-slate-200 bg-card dark:bg-slate-800 border border-border dark:border-slate-700 rounded-btn hover:border-primary/30 transition">
                    🛍️ Produits
                </a>
            </div>
        </div>

        {{-- 📈 Cartes Statistiques Premium --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
            @php
                $statCards = [
                    [
                        'label' => 'Revenu Total',
                        'value' => number_format(\App\Models\Order::whereNotIn('status', ['cancelled', 'refunded'])->sum('total') ?? 0, 2, ',', ' ') . ' €',
                        'icon' => '💰',
                        'color' => 'from-blue-500 to-blue-600 dark:from-blue-600 dark:to-blue-700',
                        'hover' => 'hover:shadow-blue-500/25',
                        'bg' => 'bg-blue-50 dark:bg-blue-900/20',
                        'route' => route('admin.orders.index'),
                    ],
                    [
                        'label' => 'Commandes Aujourd\'hui',
                        'value' => \App\Models\Order::whereDate('created_at', today())->count(),
                        'icon' => '📦',
                        'color' => 'from-purple-500 to-purple-600 dark:from-purple-600 dark:to-purple-700',
                        'hover' => 'hover:shadow-purple-500/25',
                        'bg' => 'bg-purple-50 dark:bg-purple-900/20',
                        'route' => route('admin.orders.index', ['date_from' => today()]),
                    ],
                    [
                        'label' => 'En attente',
                        'value' => \App\Models\Order::where('status', 'pending')->count(),
                        'icon' => '⏳',
                        'color' => 'from-amber-500 to-amber-600 dark:from-amber-600 dark:to-amber-700',
                        'hover' => 'hover:shadow-amber-500/25',
                        'bg' => 'bg-amber-50 dark:bg-amber-900/20',
                        'route' => route('admin.orders.index', ['status' => 'pending']),
                    ],
                    [
                        'label' => 'Clients Inscrits',
                        'value' => \App\Models\User::where('is_admin', false)->count(),
                        'icon' => '👥',
                        'color' => 'from-emerald-500 to-emerald-600 dark:from-emerald-600 dark:to-emerald-700',
                        'hover' => 'hover:shadow-emerald-500/25',
                        'bg' => 'bg-emerald-50 dark:bg-emerald-900/20',
                        'route' => '#',
                    ],
                ];
            @endphp

            @foreach($statCards as $card)
            <a href="{{ $card['route'] }}" class="card p-4 flex items-center gap-4 hover-lift transition-all duration-300 {{ $card['hover'] }} group cursor-pointer" data-aos="fade-up" data-aos-delay="{{ $loop->index * 100 }}">
                <div class="w-12 h-12 {{ $card['bg'] }} rounded-xl flex items-center justify-center text-2xl shadow-sm group-hover:scale-110 transition-transform">
                    {{ $card['icon'] }}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-xs text-secondary/60 dark:text-slate-400 uppercase tracking-wide font-medium">{{ $card['label'] }}</p>
                    <p class="text-xl font-bold bg-gradient-to-r {{ $card['color'] }} bg-clip-text text-transparent">{{ $card['value'] }}</p>
                </div>
                <div class="opacity-0 group-hover:opacity-100 transition-opacity">
                    <svg class="w-5 h-5 text-secondary/40 dark:text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                </div>
            </a>
            @endforeach
        </div>

        {{-- 📊 Graphique CA (Chart.js) --}}
        <div class="card p-6 mb-8" data-aos="fade-up" data-aos-delay="200">
            <h3 class="text-lg font-semibold text-secondary dark:text-slate-200 mb-4">📈 Évolution du CA (6 derniers mois)</h3>
            <div class="h-64 w-full">
                <canvas id="revenueChart"></canvas>
            </div>
        </div>

        {{-- 🚀 Actions Rapides --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8" data-aos="fade-up" data-aos-delay="300">
            <a href="{{ route('admin.orders.index') }}" class="card p-5 hover-lift transition group border-l-4 border-primary hover:border-primaryLight dark:border-blue-500 dark:hover:border-blue-400">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-primary/10 dark:bg-blue-500/20 rounded-lg flex items-center justify-center text-xl group-hover:scale-110 transition">📦</div>
                    <div>
                        <h4 class="font-semibold text-secondary dark:text-slate-200">Gérer les commandes</h4>
                        <p class="text-xs text-secondary/60 dark:text-slate-400">Voir, filtrer, exporter</p>
                    </div>
                </div>
            </a>
            <a href="{{ url('/produits') }}" class="card p-5 hover-lift transition group border-l-4 border-emerald-500 hover:border-emerald-400 dark:border-emerald-600 dark:hover:border-emerald-400">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-emerald-500/10 dark:bg-emerald-500/20 rounded-lg flex items-center justify-center text-xl group-hover:scale-110 transition">🛍️</div>
                    <div>
                        <h4 class="font-semibold text-secondary dark:text-slate-200">Catalogue produits</h4>
                        <p class="text-xs text-secondary/60 dark:text-slate-400">Ajouter, modifier, supprimer</p>
                    </div>
                </div>
            </a>
            <a href="{{ route('admin.orders.export.csv') }}" class="card p-5 hover-lift transition group border-l-4 border-amber-500 hover:border-amber-400 dark:border-amber-600 dark:hover:border-amber-400">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-amber-500/10 dark:bg-amber-500/20 rounded-lg flex items-center justify-center text-xl group-hover:scale-110 transition">📊</div>
                    <div>
                        <h4 class="font-semibold text-secondary dark:text-slate-200">Exports & Rapports</h4>
                        <p class="text-xs text-secondary/60 dark:text-slate-400">CSV, PDF, statistiques</p>
                    </div>
                </div>
            </a>
        </div>

        {{-- 📋 Dernières Commandes (Aperçu) --}}
        <div class="card overflow-hidden" data-aos="fade-up" data-aos-delay="400">
            <div class="p-5 border-b border-border dark:border-slate-700 flex justify-between items-center">
                <h3 class="text-lg font-semibold text-secondary dark:text-slate-200">Dernières commandes</h3>
                <a href="{{ route('admin.orders.index') }}" class="text-sm text-primary hover:underline dark:text-amber-400">Tout voir →</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="bg-gray-50 dark:bg-slate-800 text-secondary/70 dark:text-slate-400 uppercase text-xs">
                        <tr>
                            <th class="px-5 py-3">N°</th>
                            <th class="px-5 py-3">Client</th>
                            <th class="px-5 py-3">Date</th>
                            <th class="px-5 py-3">Statut</th>
                            <th class="px-5 py-3 text-right">Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border dark:divide-slate-700">
                        @forelse(\App\Models\Order::with('user')->latest()->take(5)->get() as $order)
                        <tr class="hover:bg-background/50 dark:hover:bg-slate-800/50 transition">
                            <td class="px-5 py-3 font-mono text-primary dark:text-amber-400">#{{ $order->id }}</td>
                            <td class="px-5 py-3 text-secondary dark:text-slate-200">{{ Str::limit($order->email, 20) }}</td>
                            <td class="px-5 py-3 text-secondary/60 dark:text-slate-400">{{ $order->created_at->format('d/m H:i') }}</td>
                            <td class="px-5 py-3">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium border {{ $order->status_badge_classes }}">
                                    {{ $order->status_label }}
                                </span>
                            </td>
                            <td class="px-5 py-3 text-right font-semibold text-primary dark:text-amber-400">{{ $order->formatted_total }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="px-5 py-8 text-center text-secondary/50 dark:text-slate-500">Aucune commande récente</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- Styles & Chart.js --}}
<style>
    .hover-lift { transition: transform 0.2s ease, box-shadow 0.2s ease; }
    .hover-lift:hover { transform: translateY(-2px); box-shadow: 0 10px 25px -5px rgba(0,0,0,0.1), 0 8px 10px -6px rgba(0,0,0,0.05); }
</style>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('revenueChart');
        if (ctx) {
            // Couleurs adaptatives selon le mode
            const isDark = document.documentElement.classList.contains('dark');
            const gridColor = isDark ? 'rgba(255,255,255,0.1)' : 'rgba(0,0,0,0.05)';
            const textColor = isDark ? '#94a3b8' : '#64748b';
            
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Juin'],
                    datasets: [{
                        label: 'CA (€)',
                        data: [1200, 1900, 3000, 4500, 3800, 5200], // ← Remplacer par des données dynamiques plus tard
                        borderColor: '#3B82F6',
                        backgroundColor: 'rgba(59, 130, 246, 0.15)',
                        tension: 0.4,
                        fill: true,
                        pointBackgroundColor: '#3B82F6',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2,
                        pointRadius: 4,
                        pointHoverRadius: 6
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { 
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: isDark ? '#1e293b' : '#ffffff',
                            titleColor: isDark ? '#f8fafc' : '#0f172a',
                            bodyColor: isDark ? '#cbd5e1' : '#334155',
                            borderColor: isDark ? '#334155' : '#e2e8f0',
                            borderWidth: 1,
                            padding: 10,
                            displayColors: false
                        }
                    },
                    scales: {
                        y: { 
                            beginAtZero: true, 
                            grid: { color: gridColor },
                            ticks: { color: textColor }
                        },
                        x: { 
                            grid: { display: false },
                            ticks: { color: textColor }
                        }
                    }
                }
            });
        }
    });
</script>
@endsection