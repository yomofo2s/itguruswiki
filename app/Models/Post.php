<?php

namespace App\Models;

use App\Enums\PostType;
use App\Support\Markdown;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

#[Fillable(['type', 'title', 'excerpt', 'body', 'event_starts_at', 'event_location', 'event_url', 'published_at'])]
class Post extends Model
{
    use HasFactory;

    protected $attributes = [
        'type' => 'news',
    ];

    protected function casts(): array
    {
        return [
            'type' => PostType::class,
            'published_at' => 'datetime',
            'event_starts_at' => 'datetime',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function scopePublished(Builder $query): void
    {
        $query->whereNotNull('published_at')->where('published_at', '<=', now());
    }

    public function scopeUpcomingEvents(Builder $query): void
    {
        $query->published()
            ->where('type', PostType::Event)
            ->where('event_starts_at', '>=', now()->startOfDay())
            ->orderBy('event_starts_at');
    }

    public function isPublished(): bool
    {
        return $this->published_at !== null && $this->published_at->isPast();
    }

    public function isEvent(): bool
    {
        return $this->type === PostType::Event;
    }

    public function bodyHtml(): string
    {
        return Markdown::toHtml($this->body);
    }

    public function coverUrl(): ?string
    {
        return $this->cover_path ? Storage::disk('public')->url($this->cover_path) : null;
    }
}
