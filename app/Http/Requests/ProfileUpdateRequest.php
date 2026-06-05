<?php

namespace App\Http\Requests;

use App\Models\Esporte;
use App\Models\PlayerProfile;
use App\Models\User;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        $birthDate = trim((string) $this->input('data_nascimento'));
        $username = trim((string) $this->input('username'));

        if ($username !== '') {
            $this->merge([
                'username' => str($username)->lower()->replace('@', '')->toString(),
            ]);
        }

        if (preg_match('/^\d{2}\/\d{2}\/\d{4}$/', $birthDate)) {
            $parsed = Carbon::createFromFormat('d/m/Y', $birthDate);

            if ($parsed !== false) {
                $this->merge([
                    'data_nascimento' => $parsed->format('Y-m-d'),
                ]);
            }
        }
    }

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
            'username' => [
                'nullable',
                'string',
                'min:3',
                'max:40',
                'regex:/^[a-z0-9._-]+$/',
                Rule::unique(User::class)->ignore($this->user()->id),
            ],
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
            'estado' => ['nullable', 'string', 'max:80'],
            'esporte_perfis' => ['nullable', 'array'],
            'esporte_perfis.*.esporte_id' => [
                'required',
                Rule::exists('esportes', 'id')->where(fn ($query) => $query->whereIn('slug', Esporte::ALLOWED_SLUGS)->where('ativo', true)),
            ],
            'esporte_perfis.*.posicao' => ['nullable', 'string', 'max:80'],
            'player_profile.esporte_principal_id' => [
                'nullable',
                Rule::exists('esportes', 'id')->where(fn ($query) => $query->whereIn('slug', Esporte::ALLOWED_SLUGS)->where('ativo', true)),
            ],
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
