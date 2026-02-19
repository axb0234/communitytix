<?php

namespace App\Models;

use App\Models\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TicketType extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id', 'event_id', 'name', 'price', 'capacity', 'sort_order', 'active',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'active' => 'boolean',
    ];

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function getSoldCountAttribute(): int
    {
        return $this->orderItems()
            ->whereHas('order', fn($q) => $q->whereIn('status', ['COMPLETED', 'PAID']))
            ->sum('qty');
    }

    public function getAvailableAttribute(): ?int
    {
        if ($this->capacity === null) return null;
        return max(0, $this->capacity - $this->sold_count);
    }
}
