<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;
    protected $fillable = ['name', 'email', 'password', 'branch_id', 'role', 'active'];
    protected $hidden = ['password', 'remember_token'];
    protected function casts(): array
    {
        return ['email_verified_at' => 'datetime', 'password' => 'hashed',
            'branch_id' => 'integer', 'active' => 'boolean'];
    }
    public function canReadEmployees(): bool
    {
        return $this->active && $this->branch_id !== null
            && in_array($this->role, ['viewer', 'hr_officer', 'hr_manager'], true);
    }
    public function canWriteEmployees(): bool
    {
        return $this->canReadEmployees()
            && in_array($this->role, ['hr_officer', 'hr_manager'], true);
    }
}
