<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Order extends Model
{
    /**
     * Attributs assignables en masse
     */
    protected $fillable = [
        'user_id',
        'email',
        'phone',
        'shipping_address',
        'shipping_city',
        'shipping_postal_code',
        'shipping_country',
        'delivery_method',
        'subtotal',
        'tax',
        'delivery_cost',
        'total',
        'status',
        'payment_intent_id', // Pour Stripe/PayPal plus tard
        'notes',             // Pour admin
    ];

    /**
     * Castings automatiques
     */
    protected $casts = [
        'subtotal'      => 'decimal:2',
        'tax'           => 'decimal:2',
        'delivery_cost' => 'decimal:2',
        'total'         => 'decimal:2',
        'created_at'    => 'datetime',
        'updated_at'    => 'datetime',
    ];

    /**
     * Valeur par défaut au démarrage
     */
    protected $attributes = [
        'status' => 'pending',
    ];

    // 🔑 Constantes de statut (cycle de vie commande)
    public const STATUS_PENDING    = 'pending';
    public const STATUS_PROCESSING = 'processing';
    public const STATUS_SHIPPED    = 'shipped';
    public const STATUS_DELIVERED  = 'delivered';
    public const STATUS_CANCELLED  = 'cancelled';
    public const STATUS_REFUNDED   = 'refunded';

    /**
     * Liste des statuts traduits
     */
    public static function getStatusLabels(): array
    {
        return [
            self::STATUS_PENDING    => 'En attente',
            self::STATUS_PROCESSING => 'En préparation',
            self::STATUS_SHIPPED    => 'Expédié',
            self::STATUS_DELIVERED  => 'Livré',
            self::STATUS_CANCELLED  => 'Annulé',
            self::STATUS_REFUNDED   => 'Remboursé',
        ];
    }

    /**
     * Relations Eloquent
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class)->withDefault();
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Helpers d'affichage & logique métier
     */
    public function getFormattedTotalAttribute(): string
    {
        return number_format((float) $this->total, 2, ',', ' ') . ' €';
    }

    public function getFormattedSubtotalAttribute(): string
    {
        return number_format((float) $this->subtotal, 2, ',', ' ') . ' €';
    }

    public function getStatusLabel(): string
    {
        return self::getStatusLabels()[$this->status] ?? ucfirst($this->status);
    }

    public function getStatusBadgeClasses(): string
    {
        return match ($this->status) {
            self::STATUS_PENDING    => 'bg-yellow-100 text-yellow-800 border-yellow-200',
            self::STATUS_PROCESSING => 'bg-blue-100 text-blue-800 border-blue-200',
            self::STATUS_SHIPPED    => 'bg-purple-100 text-purple-800 border-purple-200',
            self::STATUS_DELIVERED  => 'bg-green-100 text-green-800 border-green-200',
            self::STATUS_CANCELLED  => 'bg-red-100 text-red-800 border-red-200',
            self::STATUS_REFUNDED   => 'bg-gray-100 text-gray-800 border-gray-200',
            default                 => 'bg-gray-100 text-gray-800 border-gray-200',
        };
    }

    public function getDeliveryMethodLabel(): string
    {
        return match ($this->delivery_method) {
            'standard' => 'Standard (3-5 jours)',
            'express'  => 'Express (24-48h)',
            'relay'    => 'Point relais (4-6 jours)',
            default    => ucfirst($this->delivery_method),
        };
    }

    // 🛡️ Vérifications logiques
    public function canBeCancelled(): bool
    {
        return in_array($this->status, [self::STATUS_PENDING, self::STATUS_PROCESSING]);
    }

    public function isPaid(): bool
    {
        return !in_array($this->status, [self::STATUS_CANCELLED, self::STATUS_REFUNDED, self::STATUS_PENDING]);
    }

    /**
     * Scopes réutilisables
     */
    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    public function scopeRecent($query, int $limit = 10)
    {
        return $query->latest('created_at')->limit($limit);
    }

    public function scopeWithTotalRevenue($query)
    {
        return $query->whereNotIn('status', [self::STATUS_CANCELLED, self::STATUS_REFUNDED])
                     ->sum('total');
    }
}