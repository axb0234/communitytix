<?php

namespace App\Models;

use App\Models\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EventImage extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id', 'event_id', 'image_path', 'caption', 'sort_order',
    ];

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }
}
