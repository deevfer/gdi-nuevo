<?php

namespace App\Http\Requests;

use App\Models\SiteSetting;
use App\Services\RecaptchaVerifier;
use Closure;
use Illuminate\Foundation\Http\FormRequest;

class StoreContactMessageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'name' => ['required', 'string', 'max:150'],
            'email' => ['required', 'email', 'max:150'],
            'company' => ['nullable', 'string', 'max:150'],
            'phone' => ['nullable', 'string', 'max:40'],
            'subject' => ['nullable', 'string', 'max:150'],
            'message' => ['required', 'string', 'max:5000'],
            // Campo trampa: los humanos no lo ven, los bots suelen llenarlo.
            'website' => ['nullable', 'max:0'],
        ];

        // reCAPTCHA v3: solo cuando está activo en Configuración del sitio.
        $settings = SiteSetting::current();
        if ($settings->recaptchaIsConfigured()) {
            $rules['g-recaptcha-response'] = ['bail', 'required', 'string', function (string $attribute, mixed $value, Closure $fail) use ($settings): void {
                if (! app(RecaptchaVerifier::class)->verify($settings, (string) $value, 'contact', $this->ip())) {
                    $fail('No pudimos confirmar que el envío es de una persona. Intente de nuevo.');
                }
            }];
        }

        return $rules;
    }

    public function attributes(): array
    {
        return [
            'name' => 'nombre completo',
            'email' => 'email',
            'company' => 'empresa',
            'phone' => 'teléfono',
            'subject' => 'asunto',
            'message' => 'mensaje',
        ];
    }

    public function messages(): array
    {
        return [
            'required' => 'Complete el campo :attribute.',
            'email' => 'Escriba un email válido.',
            'max' => 'El campo :attribute es demasiado largo.',
            'website.max' => 'No se pudo enviar el mensaje.',
            'g-recaptcha-response.required' => 'No pudimos confirmar que el envío es de una persona. Recargue la página e intente de nuevo.',
        ];
    }
}
