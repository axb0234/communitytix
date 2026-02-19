<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Tenant extends Model
{
    protected $fillable = [
        'slug', 'name', 'tagline', 'currency', 'timezone',
        'account_type', 'tenant_active', 'sub_start_date_utc',
        'sub_end_date_utc', 'contact_email', 'logo_path',
    ];

    protected $casts = [
        'tenant_active' => 'boolean',
        'sub_start_date_utc' => 'datetime',
        'sub_end_date_utc' => 'datetime',
    ];

    public function tenantUsers(): HasMany
    {
        return $this->hasMany(TenantUser::class);
    }

    public function members(): HasMany
    {
        return $this->hasMany(Member::class);
    }

    public function carouselItems(): HasMany
    {
        return $this->hasMany(HomeCarouselItem::class)->orderBy('sort_order');
    }

    public function contentBlocks(): HasMany
    {
        return $this->hasMany(HomeContentBlock::class)->orderBy('sort_order');
    }

    public function blogPosts(): HasMany
    {
        return $this->hasMany(BlogPost::class);
    }

    public function events(): HasMany
    {
        return $this->hasMany(Event::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function paypalSetting(): HasOne
    {
        return $this->hasOne(PayPalSetting::class);
    }

    public function cashCollections(): HasMany
    {
        return $this->hasMany(CashCollection::class);
    }

    public function posPayments(): HasMany
    {
        return $this->hasMany(PosPayment::class);
    }

    public function isSubscriptionExpired(): bool
    {
        if (!$this->sub_end_date_utc) {
            return false;
        }
        return $this->sub_end_date_utc->isPast();
    }
}
