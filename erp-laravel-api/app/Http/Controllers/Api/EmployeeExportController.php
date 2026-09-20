<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Jobs\ExportEmployees;
use App\Models\EmployeeExport;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;
class EmployeeExportController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        Gate::authorize('viewAny', \App\Models\Employee::class);
        $export = EmployeeExport::create([
            'id' => (string) Str::uuid(), 'user_id' => $request->user()->id,
            'branch_id' => $request->user()->branch_id, 'status' => 'queued',
            'expires_at' => now()->addDay(),
        ]);
        ExportEmployees::dispatch($export->id)->afterCommit();
        return response()->json(['data' => $this->payload($export)], 202);
    }
    public function show(Request $request, EmployeeExport $export): JsonResponse
    {
        $this->authorizeExport($request, $export);
        return response()->json(['data' => $this->payload($export)]);
    }
    public function download(Request $request, EmployeeExport $export): StreamedResponse
    {
        $this->authorizeExport($request, $export);
        abort_unless($export->status === 'completed' && $export->path && $export->expires_at->isFuture(), 404);
        abort_unless(Storage::disk('local')->exists($export->path), 404);
        return Storage::disk('local')->download($export->path, 'employees.csv', [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }
    private function authorizeExport(Request $request, EmployeeExport $export): void
    {
        abort_unless($export->branch_id === $request->user()->branch_id
            && $export->user_id === $request->user()->id, 403);
    }
    private function payload(EmployeeExport $export): array
    {
        return ['id' => $export->id, 'status' => $export->status,
            'expires_at' => $export->expires_at?->toISOString()];
    }
}
