<?php

namespace App\Http\Requests\Vendor;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class VendorRequest extends FormRequest
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
        $id = $this->request->get("id");
        $rules =  [
            'first_name' => ['required','string', 'max:100'],
            'last_name' => ['required','string', 'max:100'], 
            //'gender' => ['required','string', 'max:100'],   
            'email' => ['required', 'email' , Rule::unique('vendors')->ignore($id)->whereNull('deleted_at') ] ,
            'password' => ['string'],
            'phone' => ['required', 'numeric', 'digits_between:11,11' ,Rule::unique('vendors')->ignore($id)->whereNull('deleted_at')],
            'store_name' => ['string', 'max:100'],   
            'store_description' => ['string'],   
            'category_id' => [ 'numeric'], 
            'city_id' => ['numeric'], 
            'logo_image' => ['image'],
            'background_image' => [ 'image'],
            'company_registration_photo' => [ 'image'],
            'national_id_front_photo' => [ 'image'],
            'national_id_back_image' => [ 'image'],
            'expiration_date' => [ 'date_format:Y-m-d'],
            
        ];
        if ($this->isMethod('POST') )
        {
            $rules['password'][] = 'required';
            $rules['store_name'][] = 'required';
            $rules['store_description'][] = 'required';
            $rules['category_id'][] = 'required';
        }
        return $rules;
    }
    public $validator = null;
    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        $this->validator = $validator;
    }
    
}
