<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BannerUnir extends Model
{
    use HasFactory;

    protected $fillable = [

        'user_id',
        'banner_nome',
        'caminho_arquivo',
        'status'
        
    ];
}
