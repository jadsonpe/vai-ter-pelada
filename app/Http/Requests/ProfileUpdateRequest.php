<?php

namespace App\Http\Requests;

use App\Models\User;
use App\Models\PlayerProfile;
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
            'data_nascimento' => ['nullable', 'date', 'before:today'],
            'avatar' => ['nullable', 'image', 'mimes:jpeg,jpg,png,webp', 'max:2048'],
            'remover_avatar' => ['nullable', 'boolean'],
            'cidade' => ['nullable', 'string', 'max:80'],
            'bairro' => ['nullable', 'string', 'max:80'],
            'logradouro' => ['nullable', 'string', 'max:120'],
            'numero' => ['nullable', 'string', 'max:20'],
            'complemento' => ['nullable', 'string', 'max:80'],
            'estado' => ['nullable', 'string', 'max:80'],
            'cep' => ['nullable', 'string', 'max:20'],
            'esporte_perfis' => ['nullable', 'array'],
            'esporte_perfis.*.esporte_id' => ['required', 'exists:esportes,id'],
            'esporte_perfis.*.posicao' => ['nullable', 'string', 'max:80'],
            'player_profile.esporte_principal_id' => ['nullable', 'exists:esportes,id'],
            'player_profile.posicao_favorita' => ['nullable', 'string', 'max:80'],
            'player_profile.headline' => ['nullable', 'string', 'max:120'],
            'player_profile.bio' => ['nullable', 'string', 'max:500'],
            'player_profile.cover_mode' => ['nullable', Rule::in(['gradient', 'image'])],
            'player_profile.banner_theme' => ['nullable', Rule::in(array_keys(PlayerProfile::gradientCoverOptions()))],
            'player_profile.banner_preset' => ['nullable', Rule::in(array_keys(PlayerProfile::imageCoverOptions()))],
            'social_links' => ['nullable', 'array'],
            'social_links.instagram' => ['nullable', 'url', 'max:255'],
            'social_links.tiktok' => ['nullable', 'url', 'max:255'],
            'social_links.youtube' => ['nullable', 'url', 'max:255'],
            'social_links.whatsapp' => ['nullable', 'string', 'max:30'],
        ];
    }
}
