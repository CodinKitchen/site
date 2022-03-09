<?php

namespace App\Entity\Enum;

enum MeetingStatus: string
{
    case DRAFT = 'draft';
    case CONFIRMED = 'confirmed';
    case CANCELED = 'canceled';
}
