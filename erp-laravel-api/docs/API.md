# API reference

All protected routes require a valid Sanctum credential, an active account, a matching token ability, and policy authorization. Responses include `X-Request-ID`.

| Method | Path | Ability | Description |
|---|---|---|---|
| GET | `/api/hello` | Public | Basic JSON route |
| GET | `/api/me` | Authenticated | Current user data |
| GET | `/api/departments` | `employees:read` | Active departments |
| GET | `/api/employees` | `employees:read` | Paginated employee list, automatically branch-scoped |
| GET | `/api/employees/{id}` | `employees:read` | One employee, branch-scoped |
| POST | `/api/employees` | `employees:write` | Create employee |
| PUT | `/api/employees/{id}` | `employees:write` | Update employee with version |
| PATCH | `/api/employees/{id}/status` | `employees:write` | Manager changes status with version |
| DELETE | `/api/employees/{id}` | `employees:write` | Manager deletes if no payroll entry exists |
| POST | `/api/employee-exports` | `employees:export` | Queues a CSV export |
| GET | `/api/employee-exports/{id}` | `employees:export` | Export status |
| GET | `/api/employee-exports/{id}/download` | `employees:export` | Download completed export |

## Employee list

Query parameters: `page`, `per_page` from 1 to 100, `search`, `department_id`, `status` (`active` or `inactive`), `sort` (`id`, `name`, `email`), and `direction` (`asc`, `desc`).

## Create employee

```json
{
  "name": "Anne Perera",
  "email": "anne@company.test",
  "department_id": 1,
  "position": "HR Executive"
}
```

The server derives `branch_id` from the authenticated user. It rejects submitted `role`, `branch_id`, or `status` fields.

## Update employee

Use the same fields as creation, plus `version` returned by the latest employee resource. The server rejects stale writes with `409` and code `STALE_EMPLOYEE_VERSION`.

## Error response

```json
{
  "message": "The submitted data is invalid.",
  "code": "VALIDATION_FAILED",
  "request_id": "uuid",
  "errors": { "email": ["The email has already been taken."] }
}
```

