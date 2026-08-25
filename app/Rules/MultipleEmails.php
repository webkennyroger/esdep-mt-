<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class MultipleEmails implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! is_string($value) || trim($value) === '') {
            return;
        }

        foreach (explode(',', $value) as $email) {
            $email = trim($email);

            if ($email !== '' && ! filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $fail('Cada e-mail da lista deve ser válido. Separe múltiplos e-mails por vírgula.');

                return;
            }
        }
    }
}
