<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Categoria extends Model
{
    use HasFactory;

    protected $fillable = ['nome'];

    public function contasPagar()
    {
        return $this->hasMany(ContasPagar::class);
    }

    public function contasReceber()
    {
        return $this->hasMany(ContasReceber::class);
    }

    public function custoVeiculo()
    {
        return $this->hasMany(CustoVeiculo::class);
    }
}
