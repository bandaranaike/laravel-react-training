<?php
namespace App\Rules;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
class CompanyEmail implements ValidationRule
{
    public function __construct(private readonly array $allowedDomains) {}
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $separator = is_string($value) ? strrpos($value, '@') : false;
        $domain = $separator === false ? '' : strtolower(substr($value, $separator + 1));
        if (! in_array($domain, $this->allowedDomains, true)) {
            $fail('Use an approved company email address.');
        }
    }
}

