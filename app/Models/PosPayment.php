<?php

namespace App\Models;

use App\Models\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PosPayment extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id', 'event_id', 'amount', 'method', 'reference',
        'status', 'recorded_by_user_id', 'recorded_at', 'notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'recorded_at' => 'datetime',
    ];

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function recordedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by_user_id');
    }
}
