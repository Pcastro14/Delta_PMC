<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class EmailTemplateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {

        $rulesArray = [
            'subject'           =>  ['required', 'string', 'max:600'],
            'content'           =>  ['required', 'string', 'max:600'],
        ];

        if ($this->isMethod('PUT')) {
            $templateId            = $this->input('id');
            $rulesArray['id']   = ['required'];
            $rulesArray['name'] = ['required', 'string', 'max:100', Rule::unique('email_templates')->ignore($templateId)];
        }else{
            $rulesArray['keys'] = ['required', 'string', 'max:600'];
            $rulesArray['name'] = ['required', 'string', 'max:100', 'unique:email_templates'];
        }
        
        return $rulesArray;
    }
}
