<?php
/**
 * enums for category
 * @author Hojjat koochak zadeh
 */

namespace App\Enums;

enum CategoryStatus: int
{
    case ACTIVE = 1;
    case INACTIVE = 2;
}
