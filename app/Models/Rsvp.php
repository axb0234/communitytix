<?php

namespace App\Models;

use App\Models\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Rsvp extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id', 'event_id', 'name', 'email', 'phone', 'guests', 'status', 'notes',
    ];

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }
}
