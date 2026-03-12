<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServidorApi extends Model
{
    use HasFactory;

    /**
     * O nome da tabela associada ao model.
     *
     * @var string
     */
    protected $table = 'servidores_api';

    /**
     * Os atributos que podem ser atribuídos em massa.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'product_id',
        'product',
        'disk_size',
        'servidor_id',
    ];

    /**
     * Os atributos que devem ser convertidos para tipos nativos.
     * Isso ajuda a garantir que os dados sejam sempre do tipo correto.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'servidor_id' => 'integer',
    ];

    /**
     * Define a relação de pertencimento com o model Servidor.
     * Um registro de 'ServidorApi' pertence a um 'Servidor'.
     */
    public function servidor(): BelongsTo
    {
        // O Laravel assume que a chave estrangeira é 'servidor_id' por padrão.
        // Se o nome da sua chave estrangeira fosse diferente, você passaria como segundo argumento.
        // Ex: return $this->belongsTo(Servidor::class, 'fk_servidor');
        return $this->belongsTo(Servidor::class);
    }
}