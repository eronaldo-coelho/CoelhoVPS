<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pagamento extends Model
{
    use HasFactory;

    /**
     * Constantes para os tipos de pagamento, para evitar "magic strings" no código.
     */
    const TIPO_PIX = 'pix';
    const TIPO_CARTAO = 'credit_card';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'contrato_id',
        'payment_id_gateway',
        'tipo_pagamento', // Novo
        'status',
        'status_detalhe', // Novo
        'valor',
        'qr_code_base64', // Específico de PIX
        'qr_code_text',   // Específico de PIX
        'metodo_pagamento', // Novo (Específico de Cartão)
        'card_last_four',   // Novo (Específico de Cartão)
        'parcelas',         // Novo (Específico de Cartão)
        'data_vencimento',
        'data_pagamento',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'data_vencimento' => 'datetime',
        'data_pagamento' => 'datetime',
        'valor' => 'decimal:2', // Boa prática para valores monetários
        'parcelas' => 'integer',
    ];

    /**
     * Get the user that owns the payment.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the contract that the payment belongs to.
     */
    public function contrato(): BelongsTo
    {
        return $this->belongsTo(Contrato::class);
    }

    /**
     * Método auxiliar para verificar se o pagamento é via PIX.
     *
     * @return bool
     */
    public function isPix(): bool
    {
        return $this->tipo_pagamento === self::TIPO_PIX;
    }

    /**
     * Método auxiliar para verificar se o pagamento é via Cartão de Crédito.
     *
     * @return bool
     */
    public function isCartaoCredito(): bool
    {
        return $this->tipo_pagamento === self::TIPO_CARTAO;
    }
}