<?php
namespace App\Exceptions;
use RuntimeException;
class EmployeeDeleteBlocked extends RuntimeException
{
    public function __construct() { parent::__construct('Employee has payroll records and cannot be deleted.'); }
}

