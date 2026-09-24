<?php

namespace App\Enums;

enum PostType: string
{
    case News = 'news';
    case Event = 'event';

    public function label(): string
    {
        return match ($this) {
            self::News => 'News',
            self::Event => 'Event',
        };
    }
}
