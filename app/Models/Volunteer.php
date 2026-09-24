<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['name', 'email', 'interest', 'skills', 'message'])]
class Volunteer extends Model
{
    use HasFactory;

    public const INTERESTS = [
        'writer' => 'Write & verify guides',
        'developer' => 'Develop the website',
        'moderator' => 'Moderate & review content',
        'events' => 'Organise events & meetups',
        'social' => 'Social media & outreach',
    ];

    protected function casts(): array
    {
        return [
            'contacted_at' => 'datetime',
        ];
    }

    public function interestLabel(): string
    {
        return self::INTERESTS[$this->interest] ?? $this->interest;
    }
}
