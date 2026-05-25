<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'apelido' => ['nullable', 'string', 'max:80'],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique(User::class)->ignore($this->user()->id),
            ],
            'phone' => ['nullable', 'string', 'max:20'],
            'avatar' => ['nullable', 'image', 'mimes:jpeg,jpg,png,webp', 'max:2048'],
            'remover_avatar' => ['nullable', 'boolean'],
            'cidade' => ['nullable', 'string', 'max:80'],
            'bairro' => ['nullable', 'string', 'max:80'],
            'logradouro' => ['nullable', 'string', 'max:120'],
            'numero' => ['nullable', 'string', 'max:20'],
            'complemento' => ['nullable', 'string', 'max:80'],
            'estado' => ['nullable', 'string', 'max:80'],
            'cep' => ['nullable', 'string', 'max:20'],
        ];
    }
}
