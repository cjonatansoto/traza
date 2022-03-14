<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;


class NeppexControl extends Model
{

    use HasFactory;

    protected $dateFormat ='Y-d-m H:i:s.v';

    protected $fillable = ['
        codaut,
        authorization_date,
        container,
        container_name,
        shipping_port_id,
        country_id,
        destination_port_id,
        exporter_id,
        border_crossing_id,
        consignee_id,
        transport_id,
        issue_certificate,
        created_by,
        updated_by,
        inactive,
        created_at,
        updated_at'
    ];



    public function shippingPort()
    {
        return $this->belongsTo('App\Models\ShippingPort', 'shipping_port_id');
    }

    public function country()
    {
        return $this->belongsTo('App\Models\Country', 'country_id');
    }

    public function destinationPort()
    {
        return $this->belongsTo('App\Models\DestinationPort', 'destination_port_id');
    }

    public function exporter()
    {
        return $this->belongsTo('App\Models\Exporter', 'exporter_id');
    }

    public function borderCrossing()
    {
        return $this->belongsTo('App\Models\BorderCrossing', 'border_crossing_id');
    }

    public function createdBy()
    {
        return $this->belongsTo('App\Models\User', 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo('App\Models\User', 'updated_by');
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->created_by = is_object(Auth::guard(config('app.guards.web'))->user()) ? Auth::guard(config('app.guards.web'))->user()->id : 1;
            $model->updated_by = is_object(Auth::guard(config('app.guards.web'))->user()) ? Auth::guard(config('app.guards.web'))->user()->id : 1;
        });

        static::updating(function ($model) {
            $model->updated_by = is_object(Auth::guard(config('app.guards.web'))->user()) ? Auth::guard(config('app.guards.web'))->user()->id : 1;
        });
    }

}
