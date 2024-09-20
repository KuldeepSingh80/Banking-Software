<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class SaveFeeRequest extends FormRequest
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
            'name' => 'required|string|max:255',
            'top_up_amount' => 'required|numeric|min:0',
            'levels' => 'required|integer',
            'partners' => 'required|integer',
            'minimum' => 'required|numeric|min:0',
            'maximum' => 'required|numeric|min:0|gte:minimum',
            'fixed_fee' => 'required|numeric|min:0',
            'percentage_fee' => 'required|numeric|min:0',
            'total_fee' => 'required|numeric|min:0',
            'levels_data' => 'required|array',
            'levels_data.*.level_index' => 'required|integer',
            'levels_data.*.base_fixed' => 'required|numeric',
            'levels_data.*.base_percentage' => 'required|numeric',
            'levels_data.*.fixed_markup_cost' => 'required|numeric',
            'levels_data.*.fixed_markup_base_cost' => 'required|numeric',
            'levels_data.*.percentage_markup_cost' => 'required|numeric',
            'levels_data.*.percentage_markup_base_cost' => 'required|numeric',
            'levels_data.*.partners' => 'required|array',
            // 'levels_data.*.partners.*.partner_index' => 'required|integer',
            // 'levels_data.*.partners.*.sharing' => 'required|numeric',
            // 'levels_data.*.partners.*.fixed_share' => 'required|numeric',
            // 'levels_data.*.partners.*.percentage_share' => 'required|numeric',
        ];
    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'result'   => 'error',
            'message'   => 'Validation errors',
            'data'      => $validator->errors()
        ],400));
    }
}
