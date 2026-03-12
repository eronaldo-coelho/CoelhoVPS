<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Cliente extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'customer_id_gateway',
        'first_name',
        'last_name',
        'phone_area_code',
        'phone_number',
        'identification_type',
        'identification_number',
        'address_zip_code',
        'address_street_name',
        'address_street_number',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}