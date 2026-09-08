<?php

namespace App\Enums;

enum AlertType: string
{
    case INFO = 'info';
    case WARNING = 'warning';
    case MAINTENANCE = 'maintenance';
    case UPDATE = 'update';
    case FORCE_UPDATE = 'force_update';
}
