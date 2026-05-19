import './bootstrap';
import Alpine from 'alpinejs';
import AOS from 'aos';
import 'aos/dist/aos.css';
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;

// 🔌 Configuration Laravel Reverb (WebSockets)
if (import.meta.env.VITE_REVERB_APP_KEY) {
    window.Echo = new Echo({
        broadcaster: 'reverb',
        key: import.meta.env.VITE_REVERB_APP_KEY,
        wsHost: import.meta.env.VITE_REVERB_HOST || '127.0.0.1',
        wsPort: import.meta.env.VITE_REVERB_PORT || 8080,
        wssPort: import.meta.env.VITE_REVERB_PORT || 8080,
        forceTLS: (import.meta.env.VITE_REVERB_SCHEME || 'http') === 'https',
        enabledTransports: ['ws', 'wss'],
        auth: {
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
            },
        },
    });
}

// ============================================
// 🛒 ALPINE.JS : OBJET GLOBAL "ecommerce"
// ============================================
Alpine.data('ecommerce', () => ({
    items: {},
    totals: { subtotal: 0, tax: 0, total: 0, items: 0 },
    isOpen: false,
    loading: false,
    pageLoading: false,

    init() {
        this.loadCart();
        this.initPageTransition();
        window.addEventListener('cart:open', () => this.isOpen = true);
    },

    async loadCart() {
        try {
            const res = await fetch('/cart/data');
            const data = await res.json();
            this.items = data.cart || {};
            this.totals = data.totals || { subtotal: 0, tax: 0, total: 0, items: 0 };
        } catch (e) { console.error('Cart load error:', e); }
    },

    formatPrice(price) {
        return new Intl.NumberFormat('fr-FR', { style: 'currency', currency: 'EUR' }).format(price);
    },

    async addToCart(productId, qty = 1) {
        this.loading = true;
        try {
            const res = await fetch('/cart/add', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ product_id: productId, qty: parseInt(qty) })
            });
            const data = await res.json();
            if (data.success) {
                this.items = data.cart;
                this.totals = data.totals;
                this.isOpen = true;
            }
        } catch (e) { console.error(e); }
        this.loading = false;
    },

    async updateQty(productId, qty) {
        qty = Math.max(1, parseInt(qty));
        try {
            const res = await fetch('/cart/update', {
                method: 'POST',
                headers: { 
                    'Content-Type': 'application/json', 
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 
                    'Accept': 'application/json' 
                },
                body: JSON.stringify({ product_id: productId, qty })
            });
            const data = await res.json();
            this.items = data.cart;
            this.totals = data.totals;
        } catch (e) { console.error(e); }
    },

    async removeItem(productId) {
        try {
            const res = await fetch('/cart/remove', {
                method: 'POST',
                headers: { 
                    'Content-Type': 'application/json', 
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 
                    'Accept': 'application/json' 
                },
                body: JSON.stringify({ product_id: productId })
            });
            const data = await res.json();
            this.items = data.cart;
            this.totals = data.totals;
        } catch (e) { console.error(e); }
    },

    initPageTransition() {
        document.body.style.opacity = '0';
        setTimeout(() => {
            document.body.style.transition = 'opacity 0.4s ease-out';
            document.body.style.opacity = '1';
        }, 50);
    },

    navigateTo(event, url) {
        if (event?.defaultPrevented) return;
        if (url?.startsWith('http') && !url.includes(window.location.hostname)) return;
        if (url?.startsWith('#') || url?.startsWith('mailto:') || url?.startsWith('tel:')) return;
        if (event?.target?.getAttribute('target') === '_blank') return;
        
        event?.preventDefault();
        this.pageLoading = true;
        document.body.style.transition = 'opacity 0.3s ease-out';
        document.body.style.opacity = '0';
        
        setTimeout(() => {
            window.location.href = url;
        }, 300);
    }
}));

// ============================================
// 🌓 MODE CLAIR/SOMBRE - Alpine.js
// ============================================
Alpine.data('theme', () => ({
    darkMode: false,
    
    init() {
        const saved = localStorage.getItem('theme');
        const systemPrefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
        this.darkMode = saved ? saved === 'dark' : systemPrefersDark;
        this.applyTheme();
        
        window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', (e) => {
            if (!localStorage.getItem('theme')) {
                this.darkMode = e.matches;
                this.applyTheme();
            }
        });
    },
    
    toggle() {
        this.darkMode = !this.darkMode;
        localStorage.setItem('theme', this.darkMode ? 'dark' : 'light');
        this.applyTheme();
    },
    
    applyTheme() {
        if (this.darkMode) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    },
    
    get icon() {
        return this.darkMode 
            ? '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/></svg>'
            : '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/></svg>';
    }
}));

// 🔍 Recherche globale (indépendante)
window.globalSearch = () => ({
    query: '',
    results: [],
    open: false,
    loading: false,
    
    async search() {
        if (this.query.length < 2) {
            this.results = [];
            return;
        }
        this.loading = true;
        try {
            const response = await fetch(`/api/products/search?q=${encodeURIComponent(this.query)}`, {
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
            });
            const data = await response.json();
            this.results = data.results;
            this.open = true;
        } catch (e) { console.error(e); }
        finally { this.loading = false; }
    }
});

// ✅ Démarrer Alpine
Alpine.start();

// 🎬 Initialisation AOS
document.addEventListener('DOMContentLoaded', () => {
    AOS.init({
        duration: 800,
        easing: 'ease-out-cubic',
        once: true,
        offset: 80,
    });
});