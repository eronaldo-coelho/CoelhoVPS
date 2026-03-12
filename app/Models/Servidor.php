<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Servidor extends Model

{
    protected $table = 'servidores';
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'vCPU',
        'ram',
        'nvme',
        'snapshots',
        'traffic',
        'mais',
        'valor',
        'desconto_percentual',
    ];

    public function servidorApi(): HasMany
    {
        return $this->hasMany(ServidorApi::class);
    }
}