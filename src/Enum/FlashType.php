<?php

namespace App\Enum;

enum FlashType: string
{
    case SUCCESS = 'success';
    case ERROR = 'error';
    case WARNING = 'warning';
    case INFO = 'info';
    case NOTICE = 'notice';
}
