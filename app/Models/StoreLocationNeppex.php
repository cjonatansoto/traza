<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StoreLocationNeppex extends Model
{
    use HasFactory;

    protected $table = 'storage_locations_neppex';

    protected $dateFormat ='Y-d-m H:i:s.v';

    protected $fillable = ['neppex_control_id, storage_location_id, created_at, updated_at'];

    public function neppexControl()
    {
        return $this->belongsTo('App\Models\NeppexControl', 'neppex_control_id');
    }

    public function storageLocation()
    {
        return $this->belongsTo('App\Models\StorageLocation', 'storage_location_id');
    }

}
