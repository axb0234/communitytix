<?php

namespace App\Models;

use App\Models\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Event extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id', 'title', 'slug', 'start_at', 'end_at', 'location',
        'location_address', 'event_type', 'status', 'flyer_path',
        'body_html', 'short_description', 'rsvp_capacity', 'published_at',
        'pwyw_enabled', 'pwyw_amount_1', 'pwyw_amount_2', 'pwyw_amount_3',
    ];

    protected $casts = [
        'start_at' => 'datetime',
        'end_at' => 'datetime',
        'published_at' => 'datetime',
        'pwyw_enabled' => 'boolean',
        'pwyw_amount_1' => 'decimal:2',
        'pwyw_amount_2' => 'decimal:2',
        'pwyw_amount_3' => 'decimal:2',
    ];

    public function images(): HasMany
    {
        return $this->hasMany(EventImage::class)->orderBy('sort_order');
    }

    public function ticketTypes(): HasMany
    {
        return $this->hasMany(TicketType::class)->orderBy('sort_order');
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function rsvps(): HasMany
    {
        return $this->hasMany(Rsvp::class);
    }

    public function cashCollections(): HasMany
    {
        return $this->hasMany(CashCollection::class);
    }

    public function posPayments(): HasMany
    {
        return $this->hasMany(PosPayment::class);
    }

    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    public function scopeUpcoming($query)
    {
        return $query->where('start_at', '>=', now());
    }

    public function isTicketed(): bool
    {
        return $this->event_type === 'TICKETED';
    }

    public function isFree(): bool
    {
        return $this->event_type === 'FREE';
    }

    public function isPwyw(): bool
    {
        return $this->isTicketed() && $this->pwyw_enabled;
    }

    public function getRsvpCountAttribute(): int
    {
        return $this->rsvps()->sum('guests');
    }

    public function getTicketsSoldAttribute(): int
    {
        return $this->orders()
            ->whereIn('status', ['COMPLETED', 'PAID'])
            ->withSum('items', 'qty')
            ->get()
            ->sum('items_sum_qty') ?? 0;
    }
}
