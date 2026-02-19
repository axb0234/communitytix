<?php

namespace App\Models;

use App\Models\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id', 'event_id', 'order_number', 'purchaser_name',
        'purchaser_email', 'purchaser_phone', 'status', 'total_amount',
        'currency', 'payment_method', 'provider_order_id',
        'provider_capture_id', 'paid_at', 'refunded_at', 'refunded_by', 'notes',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'paid_at' => 'datetime',
        'refunded_at' => 'datetime',
    ];

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function refundedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'refunded_by');
    }

    public static function generateOrderNumber(): string
    {
        return 'CTX-' . strtoupper(bin2hex(random_bytes(4))) . '-' . time();
    }
}
