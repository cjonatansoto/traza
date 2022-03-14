<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SlaughterPlaceNeppex extends Model
{
    use HasFactory;

    protected $table = 'slaughter_places_neppex';

    protected $dateFormat ='Y-d-m H:i:s.v';

    protected $fillable = ['neppex_control_id, slaughter_place_id, created_at, updated_at'];

    public function neppexControl()
    {
        return $this->belongsTo('App\Models\NeppexControl', 'neppex_control_id');
    }

    public function slaughterPlace()
    {
        return $this->belongsTo('App\Models\SlaughterPlace', 'slaughter_place_id');
    }

}
