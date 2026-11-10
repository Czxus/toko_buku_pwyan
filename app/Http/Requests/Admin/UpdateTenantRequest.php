<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class UpdateTenantRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('update', $this->route('tenant')) ?? false;
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('domain_slug')) {
            $this->merge(['domain_slug' => Str::lower(trim((string) $this->input('domain_slug')))]);
        }
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'nama_toko' => ['required', 'string', 'max:120'],
            'domain_slug' => [
                'required', 'alpha_dash', 'max:100',
                Rule::unique('tenants', 'domain_slug')->ignore($this->route('tenant')),
            ],
            'alamat_gudang' => ['required', 'string', 'max:500'],
            'kontak' => ['nullable', 'string', 'max:30'],
            'pickup_aktif' => ['nullable', 'boolean'],
            'bayar_di_tempat_aktif' => ['nullable', 'boolean'],
        ];
    }

    /** @return array<string, string> */
    public function attributes(): array
    {
        return [
            'nama_toko' => 'nama toko',
            'domain_slug' => 'slug toko',
            'alamat_gudang' => 'alamat gudang',
            'kontak' => 'kontak',
        ];
    }
}
