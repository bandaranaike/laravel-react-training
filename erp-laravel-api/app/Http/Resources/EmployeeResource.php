<?php
namespace App\Http\Resources;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
class EmployeeResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return ['id' => $this->id, 'name' => $this->name, 'email' => $this->email,
            'department_id' => $this->department_id,
            'department' => $this->whenLoaded('department', fn () => $this->department?->name),
            'position' => $this->position, 'status' => $this->status, 'version' => $this->version];
    }
}

