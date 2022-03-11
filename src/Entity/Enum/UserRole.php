<?php

namespace App\Entity\Enum;

enum UserRole
{
    case ROLE_USER;
    case ROLE_ADMIN;
    case ROLE_ATTENDEE;
}
