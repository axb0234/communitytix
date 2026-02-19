<?php

namespace App\Models;

use App\Models\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class HomeCarouselItem extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id', 'image_path', 'caption', 'subtitle', 'link_url', 'sort_order', 'active',
    ];

    protected $casts = ['active' => 'boolean'];
}
