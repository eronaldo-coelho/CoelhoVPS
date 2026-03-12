<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Regiao extends Model
{
    use HasFactory;

    protected $table = 'regioes';

    protected $fillable = [
        'regiao',
        'latencia',
        'regiao_id',
        'porcentagem',
    ];
}
