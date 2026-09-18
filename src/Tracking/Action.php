<?php

declare(strict_types=1);

namespace App\Tracking;

enum Action: string
{
    case Login = 'login';
    case Logout = 'logout';
    case Registration = 'registration';
    case ViewPage = 'view_page';
    case ButtonClick = 'button_click';
}
