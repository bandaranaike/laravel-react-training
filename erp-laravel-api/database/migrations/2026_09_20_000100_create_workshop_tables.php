<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void
    {
        Schema::create('branches', function (Blueprint $table) {
            $table->id(); $table->string('name', 100); $table->timestamps();
        });
        Schema::create('departments', function (Blueprint $table) {
            $table->id(); $table->string('name', 100)->unique();
            $table->boolean('active')->default(true); $table->timestamps();
        });
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('branch_id')->nullable()->constrained('branches');
            $table->string('role', 30)->default('viewer');
            $table->boolean('active')->default(true);
        });
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained('branches');
            $table->foreignId('department_id')->constrained('departments');
            $table->string('name', 150); $table->string('email', 254)->unique();
            $table->string('position', 100);
            $table->string('status', 20)->default('active');
            $table->unsignedInteger('version')->default(1);
            $table->timestamps();
            $table->index(['branch_id', 'id']);
            $table->index(['branch_id', 'status']);
        });
        Schema::create('audit_entries', function (Blueprint $table) {
            $table->id();
            // Preserve history even if subjects are subsequently removed.
            $table->unsignedBigInteger('actor_id');
            $table->unsignedBigInteger('branch_id');
            $table->string('action', 80); $table->string('subject_type', 150);
            $table->string('subject_id', 100);
            $table->text('before_values')->nullable();
            $table->text('after_values')->nullable();
            $table->string('request_id', 36); $table->timestamp('created_at');
        });
        Schema::create('payroll_entries', function (Blueprint $table) {
            $table->id(); $table->foreignId('employee_id')->constrained('employees');
            $table->string('status', 20)->default('draft'); $table->timestamps();
        });
        Schema::create('employee_exports', function (Blueprint $table) {
            $table->string('id', 36)->primary();
            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('branch_id')->constrained('branches');
            $table->string('status', 20)->default('queued');
            $table->string('path')->nullable();
            $table->timestamp('expires_at'); $table->timestamps();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('employee_exports');
        Schema::dropIfExists('payroll_entries');
        Schema::dropIfExists('audit_entries');
        Schema::dropIfExists('employees');
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['branch_id']);
            $table->dropColumn(['branch_id', 'role', 'active']);
        });
        Schema::dropIfExists('departments'); Schema::dropIfExists('branches');
    }
};

