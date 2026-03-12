<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sistema extends Model
{
    use HasFactory;

    protected $fillable = [
        'image_id', 'tenant_id', 'customer_id', 'name', 'description',
        'url', 'size_mb', 'uploaded_size_mb', 'os_type', 'version',
        'format', 'status', 'standard_image', 'creation_date',
        'last_modified_date', 'tags'
    ];

    protected $casts = [
        'standard_image' => 'boolean',
        'tags' => 'array',
        'creation_date' => 'datetime',
        'last_modified_date' => 'datetime',
    ];
}
