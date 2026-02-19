<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Crypt;

class PayPalSetting extends Model
{
    protected $table = 'pay_pal_settings';

    protected $fillable = [
        'tenant_id', 'mode', 'client_id_enc', 'client_secret_enc', 'webhook_id',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function setClientIdEncAttribute($value): void
    {
        $this->attributes['client_id_enc'] = Crypt::encryptString($value);
    }

    public function getClientIdDecryptedAttribute(): string
    {
        return Crypt::decryptString($this->attributes['client_id_enc']);
    }

    public function setClientSecretEncAttribute($value): void
    {
        $this->attributes['client_secret_enc'] = Crypt::encryptString($value);
    }

    public function getClientSecretDecryptedAttribute(): string
    {
        return Crypt::decryptString($this->attributes['client_secret_enc']);
    }

    public function getBaseUrlAttribute(): string
    {
        return $this->mode === 'live'
            ? 'https://api-m.paypal.com'
            : 'https://api-m.sandbox.paypal.com';
    }
}
