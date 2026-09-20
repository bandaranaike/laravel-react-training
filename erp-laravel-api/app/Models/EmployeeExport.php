<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class EmployeeExport extends Model
{
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = ['id', 'user_id', 'branch_id', 'status', 'path', 'expires_at'];
    protected function casts(): array
    {
        return ['user_id' => 'integer', 'branch_id' => 'integer', 'expires_at' => 'datetime'];
    }
}

