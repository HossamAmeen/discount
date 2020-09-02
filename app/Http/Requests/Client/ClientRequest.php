<?php

namespace App\Http\Requests\Client;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;
use Auth;
class ClientRequest extends FormRequest
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
        $id = Auth::guard('client-api')->user()->id ?? "0" ;
        //  $this->request->get("id");
        $rules =  [
            'first_name' => ['string', 'max:100'],
            'last_name' => ['string', 'max:100'], 
            'gender' => ['required','string', 'max:100'],   
            'email' => ['email' , Rule::unique('clients')->ignore($id)->whereNull('deleted_at') ] ,
            'password' => ['string'],
            'phone' => ['numeric', 'digits_between:11,11' ,Rule::unique('clients')->ignore($id)->whereNull('deleted_at')],
            
        ];
        // if ($this->isMethod('POST') )
        if(strpos($this->fullUrl(), "register") !== false)
        {
            $rules['first_name'][] = 'required';
            $rules['last_name'][] = 'required';
            $rules['email'][] = 'required';
            $rules['phone'][] = 'required';
            $rules['password'][] = 'required';
        }
        if(strpos($this->fullUrl(), "profile") !== false) { 
            $rules['first_name'][] = 'required';
            $rules['last_name'][] = 'required';
            $rules['email'][] = 'required';
            $rules['phone'][] = 'required';
        }
        return $rules;
    }
    public $validator = null;
    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        $this->validator = $validator;
    }
}