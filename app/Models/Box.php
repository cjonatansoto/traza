<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;



class Box extends Model
{

    use HasFactory;

    protected $dateFormat ='Y-d-m H:i:s.v';

    protected $fillable = [
        'overall_box',
        'neppex_control_id',
    ];

}
