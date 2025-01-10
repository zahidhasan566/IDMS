<?php

namespace App\Http\Requests\Invoice;

use Illuminate\Foundation\Http\FormRequest;

class InvoiceRequest extends FormRequest
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
            'ChassisNo'=>'required',
            'EngineNo'=>'required',
            'Price'=>'required',
            'SalesType'=>'required',
            'CustomerName'=>'required',
            'Gender'=>'required',
            'FatherName'=>'required',
            'Address'=>'required',
            'DistrictCode'=>'required',
            'InquirySale'=>'required',
            'DateOfBirth'=>'required',
            'CustomerOccupation'=>'required',
            'OwnerTyp'=>'required',
            'MotherName'=>'required',
            'Mobile'=>'required',
            'ThanaCode'=>'required',
            'NID'=>'required',
           // 'Photo'=>'required',
            'MonthlyIncome'=>'required',
            'productIntroducingMedias.*'=>'required',
            'interestInProduct'=>'required',
            'previouslyUsedBike'=>'required',
            //'MarriageDay'=>'required',
            //'SalesStaffName'=>'required',
            //'SalesStaffDesignation'=>'required',
            //'MechanicsCode'=>'required',
        ];
    }
}
