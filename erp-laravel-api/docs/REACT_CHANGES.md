# React changes for the final API

The original workshop used a plain Employee array. The complete API uses a Resource collection and pagination.

```jsx
const response = await fetch(`${API_URL}/employees?${params}`, {
  credentials: 'include',
  headers: { Accept: 'application/json', 'X-XSRF-TOKEN': getXsrfToken() },
});
const body = await response.json();
setEmployees(body.data);
setPagination(body.meta);
```

For `PUT`, retain `employee.version` inside the form and submit it. On `409` with `STALE_EMPLOYEE_VERSION`, show a message and reload the employee. For login, request `/sanctum/csrf-cookie` with `credentials: 'include'` before posting to `/login`.

