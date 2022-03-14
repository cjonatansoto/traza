<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class ViewNeppex extends Model
{

    use HasFactory;

    protected $table = 'v_neppex';

    public function scopeAuthorizationDate($query, $startDate, $endDate)
    {
        if (!is_null($startDate)) {
            if(!is_null($startDate && $endDate)){
                $startDate = date('Y-d-m H:i:s.v',strtotime($startDate));
                $endDate = date('Y-d-m 23:59:59.000',strtotime($endDate));
                $query->whereBetween('authorization_date', [$startDate, $endDate])->get();
            }
        }

        return $query;
    }

    public function scopeLoadDate($query, $startDate, $endDate)
    {
        if (!is_null($startDate)) {
            if(!is_null($startDate && $endDate)){
                $startDate = date('Y-d-m H:i:s.v',strtotime($startDate));
                $endDate = date('Y-d-m 23:59:59.000',strtotime($endDate));
                $query->whereBetween('N_FechaCarga', [$startDate, $endDate])->get();
            }
        }

        return $query;
    }

    public function scopeCodaut($query, $codaut)
    {
        if (!is_null($codaut)) {
                $query->where('N_Codaut', $codaut)->get();
        }

        return $query;
    }

    public function scopeTransferCode($query, $variable)
    {
        if (!is_null($variable)) {
            $query->where('N_CodigoTraspaso', $variable)->get();
        }

        return $query;
    }

    public function scopeCountry($query, $variable)
    {
        if (!is_null($variable)) {
            $query->where('N_PaisId', $variable)->get();
        }

        return $query;
    }

    public function scopeExporter($query, $variable)
    {
        if (!is_null($variable)) {
            $query->where('N_ExportadorId', $variable)->get();
        }

        return $query;
    }

    public function scopeBox($query, $variable)
    {
        if (!is_null($variable)) {
            $query->where('Caja_General', $variable)->get();
        }

        return $query;
    }

}
