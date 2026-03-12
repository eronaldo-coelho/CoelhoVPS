<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConfiguracaoPendente extends Model
{
    protected $table = 'configuracoes_pendentes';
    protected $fillable = ['session_id', 'user_id', 'payload'];
    protected $casts = [
        'payload' => 'array'
    ];
}