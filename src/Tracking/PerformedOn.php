<?php

declare(strict_types=1);

namespace App\Tracking;

enum PerformedOn: string
{
    case PageA = 'page_a';
    case PageB = 'page_b';
    case BuyCow = 'btn_buy_cow';
    case Download = 'btn_download';
}
