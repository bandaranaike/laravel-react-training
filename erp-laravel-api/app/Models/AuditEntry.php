<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class AuditEntry extends Model
{
    public $timestamps = false;
    protected $fillable = ['actor_id', 'branch_id', 'action', 'subject_type',
        'subject_id', 'before_values', 'after_values', 'request_id', 'created_at'];
    protected function casts(): array
    {
        return ['before_values' => 'array', 'after_values' => 'array', 'created_at' => 'datetime'];
    }
}

