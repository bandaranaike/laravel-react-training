<?php
return [
    'allowed_email_domains' => array_values(array_filter(array_map(
        fn(string $domain) => strtolower(trim($domain)),
        explode(',', env('EMPLOYEE_EMAIL_DOMAINS', 'company.test'))
    ))),
];

