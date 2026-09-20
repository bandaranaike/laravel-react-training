<?php
namespace App\Providers;
use App\Contracts\EmployeeDirectory;
use App\Models\Department;
use App\Models\Employee;
use App\Policies\EmployeePolicy;
use App\Repositories\EloquentEmployeeDirectory;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(EmployeeDirectory::class, EloquentEmployeeDirectory::class);
        // Explicit config mappings also support multiple named Oracle connections.
        foreach (config('oracle', []) as $name => $connection) {
            config(["database.connections.{$name}" => $connection]);
        }
    }
    public function boot(): void
    {
        Gate::policy(Employee::class, EmployeePolicy::class);
        Model::preventLazyLoading(! $this->app->isProduction());
        $invalidate = fn () => DB::afterCommit(fn () => Cache::forget('departments:active'));
        Department::saved($invalidate); Department::deleted($invalidate);
        RateLimiter::for('employees', fn (Request $r) => Limit::perMinute(60)->by('user:'.$r->user()->id));
        RateLimiter::for('exports', fn (Request $r) => Limit::perMinute(3)->by('user:'.$r->user()->id));
        RateLimiter::for('login', fn (Request $r) => [
            Limit::perMinute(5)->by(hash('sha256', strtolower((string) $r->input('email')).'|'.$r->ip())),
            Limit::perMinute(30)->by('ip:'.$r->ip()),
        ]);
    }
}
