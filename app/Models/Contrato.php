<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Contrato extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'servidor_id',
        'servidor_api_id',
        'regiao_id',
        'sistema_id', // Adicionado
        'vCPU',
        'ram',
        'disk_info',
        'snapshots',
        'traffic',
        'regiao_nome',
        'sistema_nome', // Adicionado
        'valor_total_mensal',
        'status',
        'data_proximo_vencimento',
        'metodo_pagamento',
        'gateway_card_id', // Também adicione este, pois você o usa no controller
    ];

    protected $casts = [
        'data_proximo_vencimento' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function servidor(): BelongsTo
    {
        return $this->belongsTo(Servidor::class);
    }

    public function servidorApi(): BelongsTo
    {
        return $this->belongsTo(ServidorApi::class, 'servidor_api_id');
    }

    public function regiao(): BelongsTo
    {
        return $this->belongsTo(Regiao::class);
    }

    /**
     * Define a relação com o Sistema Operacional.
     */
    public function sistema(): BelongsTo
    {
        return $this->belongsTo(Sistema::class);
    }


    /**
     * Define a relação de que um Contrato tem uma InstanciaVps.
     */
    public function instancia(): HasOne
    {
        return $this->hasOne(InstanciaVps::class, 'contrato_id');
    }

    public function pagamentos(): HasMany
    {
        return $this->hasMany(Pagamento::class);
    }

}