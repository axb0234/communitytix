<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuditLog extends Model
{
    protected $fillable = [
        'tenant_id', 'user_id', 'action', 'entity_type', 'entity_id',
        'details', 'ip_address',
    ];

    protected $casts = [
        'details' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function log(string $action, ?string $entityType = null, ?int $entityId = null, ?array $details = null): self
    {
        return self::create([
            'tenant_id' => app()->bound('current_tenant') ? app('current_tenant')->id : null,
            'user_id' => auth()->id(),
            'action' => $action,
            'entity_type' => $entityType,
            'entity_id' => $entityId,
            'details' => $details,
            'ip_address' => request()->ip(),
        ]);
    }
}
