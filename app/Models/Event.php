<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Event extends Model
{
    protected $fillable = [
        'title', 'description', 'start_date', 'end_date', 'location',
        'capacity', 'category_id', 'club_id', 'created_by',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'datetime',
            'end_date' => 'datetime',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /** Null when the event is organised by the university itself. */
    public function club(): BelongsTo
    {
        return $this->belongsTo(Club::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function registrations(): HasMany
    {
        return $this->hasMany(Registration::class);
    }

    public function attendees(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'registrations')->withTimestamps();
    }

    public function scopeUpcoming(Builder $query): Builder
    {
        return $query->where('end_date', '>=', now());
    }

    public function organizerName(): string
    {
        return $this->club?->name ?? 'University administration';
    }

    public function isPast(): bool
    {
        return $this->end_date->isPast();
    }

    public function registeredCount(): int
    {
        return $this->registrations_count ?? $this->registrations()->count();
    }

    public function seatsLeft(): int
    {
        return max(0, $this->capacity - $this->registeredCount());
    }

    public function isFull(): bool
    {
        return $this->seatsLeft() === 0;
    }
}
