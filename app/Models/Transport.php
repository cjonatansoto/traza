<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;


class Transport extends Model
{

    use HasFactory;

    protected $dateFormat ='Y-d-m H:i:s.v';

    protected $fillable = [
        'name'
    ];
}
