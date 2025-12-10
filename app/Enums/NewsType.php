<?php

namespace App\Enums;

enum NewsType: int
{
    case OUTSTANDING = 1; // nổ i bật 
    case EVENT = 2; // Sự kiện 
    case PROJECT = 3; // Dự án 
}
