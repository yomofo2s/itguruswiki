<?php

namespace App\Enums;

enum Role: string
{
    case Admin = 'admin';
    case Editor = 'editor';
    case Member = 'member';

    public function label(): string
    {
        return match ($this) {
            self::Admin => 'Administrator',
            self::Editor => 'Editor',
            self::Member => 'Member',
        };
    }

    /** Editors and admins can review and publish content. */
    public function canModerate(): bool
    {
        return in_array($this, [self::Admin, self::Editor], true);
    }
}
