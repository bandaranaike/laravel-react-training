<?php
namespace App\Listeners;
use App\Events\EmployeeCreated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;
class NotifyHrOfEmployeeCreation implements ShouldQueue
{
    public int $tries = 3;
    public function handle(EmployeeCreated $event): void
    {
        // Local workshop notification sink. No external email is sent.
        Log::info('HR notification: employee created', [
            'employee_id' => $event->employeeId, 'actor_id' => $event->actorId,
        ]);
    }
}

