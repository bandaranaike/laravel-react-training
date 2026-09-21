<?php
use App\Exceptions\EmployeeDeleteBlocked;
use App\Exceptions\StaleEmployeeVersion;
use App\Http\Middleware\AssignRequestId;
use App\Http\Middleware\EnsureActiveAccount;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Laravel\Sanctum\Http\Middleware\CheckAbilities;
use Symfony\Component\HttpFoundation\Response;
return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(web: __DIR__.'/../routes/web.php', api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php', health: '/up')
    ->withEvents(discover: [__DIR__.'/../app/Listeners'])
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->prepend(AssignRequestId::class);
        $middleware->statefulApi();
        $middleware->alias(['active' => EnsureActiveAccount::class, 'abilities' => CheckAbilities::class]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(fn (Request $r, Throwable $e) =>
            $r->is('api/*', 'login', 'logout', 'sanctum/*') || $r->expectsJson());
        $exceptions->render(function (EmployeeDeleteBlocked $e, Request $r) {
            return response()->json(['message' => $e->getMessage(), 'code' => 'EMPLOYEE_DELETE_BLOCKED'], 409);
        });
        $exceptions->render(function (StaleEmployeeVersion $e, Request $r) {
            return response()->json(['message' => $e->getMessage(), 'code' => 'STALE_EMPLOYEE_VERSION'], 409);
        });
        $exceptions->respond(function (Response $response) {
            if (! request()->is('api/*', 'login', 'logout', 'sanctum/*')) { return $response; }
            $status = $response->getStatusCode();
            $id = request()->attributes->get('request_id');
            $response->headers->set('X-Request-ID', (string) $id);
            if ($status < 400) { return $response; }
            $body = json_decode($response->getContent(), true) ?: [];
            $messages = [401 => 'Authentication required.', 403 => 'This action is forbidden.',
                404 => 'Resource not found.', 405 => 'Method not allowed.', 409 => 'Operation conflicts with current data.',
                419 => 'Session expired. Refresh and try again.', 422 => 'The submitted data is invalid.',
                429 => 'Too many requests. Try again later.'];
            $safe = ['message' => $messages[$status] ?? 'The request could not be completed.',
                'code' => $body['code'] ?? 'HTTP_'.$status, 'request_id' => $id];
            if ($status === 409 && isset($body['code'])) { $safe['message'] = $body['message']; }
            if ($status === 422) { $safe['code'] = 'VALIDATION_FAILED'; $safe['errors'] = $body['errors'] ?? []; }
            if ($status >= 500) { $safe['message'] = 'An unexpected server error occurred.'; $safe['code'] = 'SERVER_ERROR'; }
            if (in_array($status, [401, 403], true)) {
                logger()->notice('Access rejected', ['status' => $status, 'request_id' => $id]);
            }
            // Preserve cookies, Retry-After, Allow and other framework response headers.
            $response->setContent(json_encode($safe, JSON_THROW_ON_ERROR));
            $response->headers->set('Content-Type', 'application/json');
            $response->headers->remove('Content-Length');
            return $response;
        });
    })->create();
