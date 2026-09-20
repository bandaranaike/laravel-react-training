<?php
namespace App\Jobs;
use App\Models\Employee;
use App\Models\EmployeeExport;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
class ExportEmployees implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public int $tries = 3;
    public int $timeout = 120;
    public function __construct(public readonly string $exportId) {}
    public function backoff(): array { return [10, 60, 300]; }
    public function handle(): void
    {
        $export = EmployeeExport::query()->findOrFail($this->exportId);
        if ($export->status === 'completed') { return; } // Safe retry after completion.
        $export->update(['status' => 'processing']);
        try {
            $path = 'exports/'.$export->id.'.csv';
            $stream = fopen('php://temp', 'w+');
            fputcsv($stream, ['ID', 'Name', 'Email', 'Department', 'Position', 'Status']);
            Employee::query()->with('department')->where('branch_id', $export->branch_id)
                ->orderBy('id')->chunkById(500, function ($employees) use ($stream) {
                    foreach ($employees as $employee) {
                        fputcsv($stream, [$employee->id, $employee->name, $employee->email,
                            $employee->department?->name, $employee->position, $employee->status]);
                    }
                });
            rewind($stream);
            Storage::disk('local')->put($path, stream_get_contents($stream));
            fclose($stream);
            $export->update(['status' => 'completed', 'path' => $path]);
        } catch (\Throwable $error) {
            $export->update(['status' => 'failed']);
            Log::error('Employee export failed', ['export_id' => $export->id, 'exception' => $error]);
            throw $error;
        }
    }
}

