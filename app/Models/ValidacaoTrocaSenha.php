<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ValidacaoTrocaSenha extends Model
{
    protected $fillable = [
        'user_id',
        'email',
        'token',
        'usado',
        'data_horario'
    ];

    protected $casts = [
        'data_horario' => 'datetime',
        'usado' => 'boolean'
    ];
}