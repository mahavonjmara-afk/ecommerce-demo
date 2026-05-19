@extends('layouts.app')

@section('content')
<div class="py-8 md:py-12 bg-background/50 dark:bg-slate-950/50 min-h-screen transition-colors duration-300">
    <div class="container mx-auto px-4 max-w-7xl">
        
        {{-- En-tête Dashboard --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-8 gap-4" data-aos="fade-down">
            <div>
                <h1 class="text-2xl md:text-3xl font-bold text-secondary dark:text-slate-100 mb-1">📊 Dashboard Commandes</h1>
                <p class="text-sm text-secondary/60 dark:text-slate-400">Gérez vos commandes, suivez les statuts et exportez les données</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('admin.orders.export.csv') }}" class="btn-primary px-4 py-2 text-sm flex items-center gap-2 bg-emerald-600 hover:bg-emerald-500 dark:bg-emerald-700 dark:hover:bg-emerald-600">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    Export CSV
                </a>
                <a href="{{ route('admin.orders.export.pdf') }}" class="px-4 py-2 text-sm font-medium text-secondary dark:text-slate-200 bg-card dark:bg-slate-800 border border-border dark:border-slate-700 rounded-btn hover:border-primary/30 dark:hover:border-amber-400 transition">
                    📄 PDF
                </a>
            </div>
        </div>

        {{-- 📈 Cartes Statistiques Premium --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8" data-aos="fade-up" data-aos-delay="100">
            @php
                $statCards = [
                    [
                        'label' => 'Revenu Total',
                        'value' => number_format($stats['total_revenue'] ?? 0, 2, ',', ' ') . ' €',
                        'icon' => '💰',
                        'color' => 'from-blue-500 to-blue-600 dark:from-blue-600 dark:to-blue-700',
                        'hover' => 'hover:shadow-blue-500/25',
                        'bg' => 'bg-blue-50 dark:bg-blue-900/20',
                    ],
                    [
                        'label' => 'Commandes Aujourd\'hui',
                        'value' => $stats['today_orders'] ?? 0,
                        'icon' => '📦',
                        'color' => 'from-purple-500 to-purple-600 dark:from-purple-600 dark:to-purple-700',
                        'hover' => 'hover:shadow-purple-500/25',
                        'bg' => 'bg-purple-50 dark:bg-purple-900/20',
                    ],
                    [
                        'label' => 'En attente',
                        'value' => $stats['pending'] ?? 0,
                        'icon' => '⏳',
                        'color' => 'from-amber-500 to-amber-600 dark:from-amber-600 dark:to-amber-700',
                        'hover' => 'hover:shadow-amber-500/25',
                        'bg' => 'bg-amber-50 dark:bg-amber-900/20',
                    ],
                    [
                        'label' => 'En préparation',
                        'value' => $stats['processing'] ?? 0,
                        'icon' => '🔄',
                        'color' => 'from-emerald-500 to-emerald-600 dark:from-emerald-600 dark:to-emerald-700',
                        'hover' => 'hover:shadow-emerald-500/25',
                        'bg' => 'bg-emerald-50 dark:bg-emerald-900/20',
                    ],
                ];
            @endphp

            @foreach($statCards as $card)
            <div class="card p-4 flex items-center gap-4 hover-lift transition-all duration-300 {{ $card['hover'] }} group" data-aos="fade-up" data-aos-delay="{{ $loop->index * 100 }}">
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
            </div>
            @endforeach
        </div>

        {{-- 🔍 Filtres Premium --}}
        <div class="card p-4 mb-6" data-aos="fade-up" data-aos-delay="200">
            <form method="GET" action="{{ route('admin.orders.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
                <div class="lg:col-span-2">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Rechercher (ID, email, ville)" class="input-field dark:bg-slate-800 dark:border-slate-600 dark:text-slate-200 dark:placeholder-slate-400">
                </div>
                <div>
                    <select name="status" class="input-field dark:bg-slate-800 dark:border-slate-600 dark:text-slate-200">
                        <option value="">Tous les statuts</option>
                        @foreach(\App\Models\Order::getStatusLabels() as $key => $label)
                            <option value="{{ $key }}" {{ request('status') == $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <input type="date" name="date_from" value="{{ request('date_from') }}" class="input-field dark:bg-slate-800 dark:border-slate-600 dark:text-slate-200">
                </div>
                <div>
                    <input type="date" name="date_to" value="{{ request('date_to') }}" class="input-field dark:bg-slate-800 dark:border-slate-600 dark:text-slate-200">
                </div>
                <div class="lg:col-span-5 flex gap-2">
                    <button type="submit" class="btn-primary px-6 text-sm bg-primary hover:bg-primaryLight dark:bg-blue-600 dark:hover:bg-blue-500">
                        🔍 Filtrer
                    </button>
                    <a href="{{ route('admin.orders.index') }}" class="px-4 py-2 text-sm font-medium text-secondary/70 dark:text-slate-400 bg-gray-100 dark:bg-slate-800 hover:bg-gray-200 dark:hover:bg-slate-700 rounded-btn transition">
                        Réinitialiser
                    </a>
                </div>
            </form>
        </div>

        {{-- 📋 Tableau des Commandes Premium --}}
        <div class="card overflow-hidden" data-aos="fade-up" data-aos-delay="300">
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="bg-gray-50 dark:bg-slate-800 text-secondary/70 dark:text-slate-400 uppercase text-xs font-semibold">
                        <tr>
                            <th class="px-6 py-4">Commande</th>
                            <th class="px-6 py-4">Client</th>
                            <th class="px-6 py-4">Date</th>
                            <th class="px-6 py-4">Statut</th>
                            <th class="px-6 py-4 text-right">Total</th>
                            <th class="px-6 py-4 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border dark:divide-slate-700">
                        @forelse($orders as $order)
                        <tr class="hover:bg-background/50 dark:hover:bg-slate-800/50 transition group">
                            <td class="px-6 py-4">
                                <span class="font-mono font-semibold text-primary dark:text-amber-400">#{{ $order->id }}</span>
                                <div class="text-xs text-secondary/50 dark:text-slate-500 mt-0.5">{{ $order->items->count() }} article(s)</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-medium text-secondary dark:text-slate-200">{{ Str::limit($order->email, 25) }}</div>
                                <div class="text-xs text-secondary/50 dark:text-slate-500">{{ $order->shipping_city }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-secondary/70 dark:text-slate-400">{{ $order->created_at->format('d/m/Y') }}</div>
                                <div class="text-xs text-secondary/50 dark:text-slate-500">{{ $order->created_at->format('H:i') }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium border {{ $order->status_badge_classes }} dark:bg-slate-800">
                                    {{ $order->status_label }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <span class="font-bold text-primary dark:text-amber-400">{{ $order->formatted_total }}</span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <form method="POST" action="{{ route('admin.orders.update-status', $order) }}" class="inline-flex">
                                    @csrf
                                    <select name="status" 
                                            class="text-xs border-border dark:border-slate-600 rounded-md px-2 py-1.5 bg-card dark:bg-slate-800 focus:ring-2 focus:ring-primary/20 focus:border-primary focus:outline-none transition cursor-pointer hover:border-primary/50 dark:hover:border-amber-400" 
                                            onchange="this.form.submit()"
                                            title="Changer le statut">
                                        @foreach(\App\Models\Order::getStatusLabels() as $key => $label)
                                            <option value="{{ $key }}" {{ $order->status == $key ? 'selected' : '' }}>
                                                {{ $label }}
                                            </option>
                                        @endforeach
                                    </select>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <div class="text-secondary/40 dark:text-slate-600 mb-2 text-4xl">📦</div>
                                <p class="text-secondary/60 dark:text-slate-400 font-medium">Aucune commande trouvée</p>
                                <p class="text-xs text-secondary/40 dark:text-slate-500 mt-1">Les commandes apparaîtront ici dès qu'un client passera commande</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            {{-- Pagination --}}
            @if($orders->hasPages())
            <div class="p-4 border-t border-border dark:border-slate-700 bg-gray-50/50 dark:bg-slate-800/50">
                {{ $orders->links() }}
            </div>
            @endif
        </div>
    </div>
</div>

{{-- Styles spécifiques au dashboard admin --}}
<style>
    .hover-lift { transition: transform 0.2s ease, box-shadow 0.2s ease; }
    .hover-lift:hover { transform: translateY(-2px); box-shadow: 0 10px 25px -5px rgba(0,0,0,0.1), 0 8px 10px -6px rgba(0,0,0,0.05); }
</style>
@endsection