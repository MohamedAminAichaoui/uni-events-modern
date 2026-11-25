<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    public const ROLE_ADMIN = 'admin';
    public const ROLE_CLUB_MANAGER = 'club_manager';
    public const ROLE_STUDENT = 'student';

    protected $fillable = ['name', 'email', 'password', 'role', 'club_id'];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /** The club this user manages (club managers only). */
    public function club(): BelongsTo
    {
        return $this->belongsTo(Club::class);
    }

    public function registeredEvents(): BelongsToMany
    {
        return $this->belongsToMany(Event::class, 'registrations')->withTimestamps();
    }

    public function joinedClubs(): BelongsToMany
    {
        return $this->belongsToMany(Club::class, 'club_members')->withTimestamps();
    }

    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    public function isClubManager(): bool
    {
        return $this->role === self::ROLE_CLUB_MANAGER;
    }

    /** Admins manage every event; a club manager only manages events of their own club. */
    public function canManage(Event $event): bool
    {
        return $this->isAdmin()
            || ($this->isClubManager() && $this->club_id !== null && $this->club_id === $event->club_id);
    }
}
