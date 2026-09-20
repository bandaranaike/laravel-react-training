<?php
use App\Rules\CompanyEmail;
it('accepts only approved domains', function () {
    $rule = new CompanyEmail(['company.test']); $errors = [];
    $rule->validate('email', 'person@company.test', function (string $message) use (&$errors) { $errors[] = $message; });
    expect($errors)->toBe([]);
    $rule->validate('email', 'person@example.com', function (string $message) use (&$errors) { $errors[] = $message; });
    expect($errors)->not->toBe([]);
});

