<?php

namespace App\Http\Requests\File;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class CreateFileRequest extends FormRequest
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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'file_name'  => 'required|string|max:255',
            'size'       => 'required|integer|min:1',
            'folder_id' => 'nullable|exists:folders,id',
            'visibility' => 'required|in:public,private'
        ];
    }
     public function validated($key = null, $default = null)
    {
        $data = parent::validated($default, $default);
        $data['folder_id'] = $this->route('folder');
        $data['user_id'] = auth()->id();
        return $data;
    }
}
