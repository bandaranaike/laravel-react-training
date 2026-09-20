<?php
namespace App\Exceptions;
use RuntimeException;
class StaleEmployeeVersion extends RuntimeException
{
    public function __construct() { parent::__construct('Employee changed. Reload it before saving.'); }
}

