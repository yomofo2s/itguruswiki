<?php

namespace App\Enums;

enum ArticleStatus: string
{
    case Draft = 'draft';
    case Pending = 'pending';
    case Published = 'published';
    case Rejected = 'rejected';

    public function label(): string
    {
        return match ($this) {
            self::Draft => 'Draft',
            self::Pending => 'Waiting for review',
            self::Published => 'Published',
            self::Rejected => 'Changes requested',
        };
    }

    /** Tailwind classes for a status badge. */
    public function badge(): string
    {
        return match ($this) {
            self::Draft => 'bg-slate-100 text-slate-700',
            self::Pending => 'bg-amber-100 text-amber-800',
            self::Published => 'bg-emerald-100 text-emerald-800',
            self::Rejected => 'bg-rose-100 text-rose-800',
        };
    }
}
