<?php

namespace App\Enums;

enum StaffRole: string
{
    case Administrator = 'administrator';
    case Dispatcher = 'dispatcher';
    case Technician = 'technician';
}
