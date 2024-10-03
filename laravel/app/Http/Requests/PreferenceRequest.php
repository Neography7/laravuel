<?php

namespace App\Http\Requests;

class PreferenceRequest extends BaseRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'categories' => 'array|nullable',
            'categories.*' => 'string|max:255',
            'sources' => 'array|nullable',
            'sources.*' => 'string|max:255|sometimes|in:newsapi,guardian,nytimes',
            'authors' => 'array|nullable',
            'authors.*' => 'string|max:255',
        ];
    }
}
