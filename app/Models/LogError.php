<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;


class LogError extends Model
{

    use HasFactory;

    protected $dateFormat ='Y-d-m H:i:s.v';

    protected $table = 'log_erros';

    protected $fillable = [
        'code',
        'file',
        'line',
        'message'
    ];

}
