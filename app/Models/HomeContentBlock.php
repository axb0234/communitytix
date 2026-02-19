<?php

namespace App\Models;

use App\Models\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class HomeContentBlock extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id', 'title', 'body_html', 'icon', 'sort_order', 'active',
    ];

    protected $casts = ['active' => 'boolean'];
}
