<?php

namespace App\Support;

use Illuminate\Support\Str;

class Markdown
{
    /**
     * Render user-written Markdown safely: raw HTML is escaped and
     * javascript:/data: links are removed.
     */
    public static function toHtml(?string $markdown): string
    {
        return Str::markdown((string) $markdown, [
            'html_input' => 'escape',
            'allow_unsafe_links' => false,
            'max_nesting_level' => 20,
        ]); // GitHub-flavoured Markdown (tables, strikethrough, autolinks)
    }

    /** Estimated reading time in minutes. */
    public static function readingTime(?string $markdown): int
    {
        return max(1, (int) ceil(str_word_count(strip_tags((string) $markdown)) / 200));
    }
}
