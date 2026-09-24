<?php

namespace Tests\Unit;

use App\Support\Markdown;
use PHPUnit\Framework\TestCase;

class MarkdownTest extends TestCase
{
    public function test_renders_github_flavoured_markdown(): void
    {
        $html = Markdown::toHtml("## Steps\n\n| Doc | Needed |\n|---|---|\n| Passport | yes |\n\n~~old~~");

        $this->assertStringContainsString('<h2>Steps</h2>', $html);
        $this->assertStringContainsString('<table>', $html);
        $this->assertStringContainsString('<del>old</del>', $html);
    }

    public function test_escapes_html_and_strips_unsafe_links(): void
    {
        $html = Markdown::toHtml("<script>alert(1)</script>\n\n[x](javascript:alert(1))");

        $this->assertStringNotContainsString('<script>', $html);
        $this->assertStringNotContainsString('javascript:', $html);
        $this->assertStringContainsString('&lt;script&gt;', $html);
    }

    public function test_reading_time_is_at_least_one_minute(): void
    {
        $this->assertSame(1, Markdown::readingTime(''));
        $this->assertSame(2, Markdown::readingTime(str_repeat('word ', 350)));
    }
}
