<?php

namespace App\Models;

use App\Models\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CashCollection extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id', 'event_id', 'amount', 'collected_by_user_id', 'collected_at', 'notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'collected_at' => 'datetime',
    ];

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function collectedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'collected_by_user_id');
    }
}
