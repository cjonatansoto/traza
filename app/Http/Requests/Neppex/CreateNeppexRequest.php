<?php

namespace App\Http\Requests\Neppex;

use Illuminate\Foundation\Http\FormRequest;


class CreateNeppexRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'codaut' => 'required|unique:neppex_controls,codaut',
            'transfer_code' => 'required|unique:neppex_controls,transfer_code',
            'authorization_date' => 'required|date',
            'container' => 'required',
            'container_name' => 'required',
            'issue_certificate' => 'required',
            'shipping_port_id' => 'required',
            'country_id' => 'required',
            'destination_port_id' => 'required',
            'export_id' => 'required',
            'border_crossing_id' => 'required',
            'consignee_id' => 'required',
            'storage_location_id' => 'required',
            'slaughter_place_id' => 'required',
            'transport_id' => 'required',
            'box' => 'required',
        ];
    }

    public function messages()
    {
        return [
            'codaut.required'         => 'El campo numero de neppex/codaut es requerido!',
            'authorization_date.required'  => 'El campo fecha de autorización es requerido!',
            'authorization_date.date'  => 'El campo fecha de autorización es requerido!',
            'container.required' => 'El campo contenedor es requerido!',
            'container_name.required' => 'El campo nombre contenedor es requerido!',
            'shipping_port_id.required' => 'El campo puerto embarque es requerido!',
            'country_id.required' => 'El campo pais destino es requerido!',
            'destination_port_id.required' => 'El campo puerto destino es requerido!',
            'export_id.required' => 'El campo exportador es requerido!',
            'border_crossing_id.required' => 'El campo agencia de aduana es requerido!',
            'consignee_id.required' => 'El campo consignatario es requerido!',
            'storage_location_id.required' => 'El campo lugar almacenamiento es requerido!',
            'slaughter_place_id.required' => 'El campo lugar faenamiento es requerido!',
            'box.required'    => 'El TXT es requerido',
            'transfer_code.required'  => 'El campo codigo de traspaso requerido!',
        ];
    }
}
