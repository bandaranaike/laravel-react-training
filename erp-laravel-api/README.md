# Laravel Employee API workshop code

This project completes the examples from the Laravel workshop deck. It targets Laravel 13, PHP 8.3+, Sanctum, and a React client. Start with a fresh Laravel application, then copy these files into it.

## Install

```bash
composer create-project laravel/laravel employee-api "^13.0"
cd employee-api
php artisan install:api
composer require yajra/laravel-oci8:"^13.0" # Only after OCI8 and Oracle Client are installed.
php artisan vendor:publish --tag=oracle # Required only for Oracle.
```

Copy the workshop files over the generated project. Create a local SQLite database or configure a dedicated MySQL/Oracle workshop schema. Then configure `.env`:

```bash
cp .env.example .env
php artisan key:generate
# Set WORKSHOP_SEED_PASSWORD to a temporary local value.
php artisan migrate
php artisan db:seed
php artisan queue:work
php artisan serve
```

Run checks:

```bash
php artisan test
./vendor/bin/pint --test
php artisan route:list --path=api
```

## Login and API access

The React SPA uses cookie authentication. Start by requesting `/sanctum/csrf-cookie` with credentials, then `POST /login`, then call the API with `credentials: 'include'` and the `X-XSRF-TOKEN` header. The seed users are `viewer@company.test`, `hr_officer@company.test`, and `hr_manager@company.test`. Each uses `WORKSHOP_SEED_PASSWORD`.

For Postman, create a token through Tinker. Tokens need explicit abilities. The protected routes require `employees:read`, `employees:write`, or `employees:export` as appropriate.

```php
$user->createToken('postman', ['employees:read', 'employees:write', 'employees:export'], now()->addHour())->plainTextToken;
```

## Response contract

`GET /api/employees` returns a paginated Resource collection. React reads employees from `response.data.data` and pagination metadata from `response.data.meta`. Write operations need the resource `version` so updates detect stale edits.

## Oracle

The OCI8 extension and Oracle Instant Client must be available in the PHP runtime used by HTTP and queue workers. Configure the values documented in `config/oracle.php`, use a dedicated schema, and run:

```bash
php --ri oci8
php artisan tinker
# DB::connection('oracle')->select('select 1 as value from dual');
```

Do not run destructive workshop migrations against an existing ERP schema. Oracle tables, identity generation, triggers, dates, and stored procedures need integration tests against the target version.

## Deliberate workshop boundaries

The `employee_exports` download method permits only the exporting user in the same branch. A production application may add manager delegation through a separately reviewed authorization rule. The export job writes to Laravel local storage for the workshop; production should use appropriate encrypted object storage and retention policy.

`Department` caching uses one global key because department data is global in this sample. Include tenant or branch scope in cache keys if that changes.

