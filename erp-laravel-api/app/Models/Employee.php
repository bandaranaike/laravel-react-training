<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class Employee extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'email', 'department_id', 'position', 'status'];
    protected function casts(): array
    {
        return ['branch_id' => 'integer', 'department_id' => 'integer', 'version' => 'integer'];
    }
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }
}

