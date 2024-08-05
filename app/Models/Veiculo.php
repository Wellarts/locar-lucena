<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Veiculo extends Model
{
    use HasFactory, LogsActivity;
   
    protected $fillable = [
        'marca_id',
        'modelo',
        'ano',
        'placa',
        'cor',
        'km_atual',
        'valor_diaria',
        'prox_troca_oleo',
        'prox_troca_filtro',
        'aviso_troca_oleo',
        'aviso_troca_filtro',
        'prox_troca_correia',
        'prox_troca_pastilha',
        'aviso_troca_correia',
        'aviso_troca_pastilha',
        'chassi',
        'data_compra',
        'status',
        'status_alerta',
        'status_locado'
    ];

    public function Marca()
    {
        return $this->belongsTo(Marca::class);
    }

    public function Agendamento()
    {
        return $this->hasMany(Agendamento::class);
    }

    public function CustoVeiculo()
    {
        return $this->hasMany(CustoVeiculo::class);
    }

    public function Temp_lucratividade()
    {
        return $this->hasMany(Temp_lucratividade::class);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
        ->logOnly(['*']);
        // Chain fluent methods for configuration options
    }
}
