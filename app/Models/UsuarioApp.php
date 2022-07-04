<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UsuarioApp extends Model
{
    use HasFactory;

    protected $fillable = [

        'user_id',
        'nome',
        'sobrenome',
        'status',
        'foto_usuario',

    ];

}
