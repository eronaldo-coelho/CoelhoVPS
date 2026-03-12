<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InstanciaVps extends Model {
    use HasFactory;
    protected $table = 'instancias_vps';
    protected $fillable = [
        'user_id',
        'contrato_id',
        'instance_id_contabo',
        'display_name',
        'ip_v4',
        'status',
        'root_password',
        'full_response_data',
    ];
    protected $casts = [
        'full_response_data' => 'array',
    ];
    public function user(): BelongsTo {
        return $this->belongsTo(User::class);
    }
    public function contrato(): BelongsTo {
        return $this->belongsTo(Contrato::class);
    }
}