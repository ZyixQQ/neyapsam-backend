<?php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'avatar_url',
        'device_id',
        'post_count',
        'is_banned',
        'is_admin',
        'push_token',
        'platform',
        'push_notifications_enabled',
        'push_token_updated_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at'           => 'datetime',
            'push_token_updated_at'       => 'datetime',
            'is_banned'                   => 'boolean',
            'is_admin'                    => 'boolean',
            'push_notifications_enabled'  => 'boolean',
            'password'                    => 'hashed',
        ];
    }

    public function suggestions(): HasMany
    {
        return $this->hasMany(Suggestion::class);
    }

    public function votes(): HasMany
    {
        return $this->hasMany(Vote::class);
    }

    public function reports(): HasMany
    {
        return $this->hasMany(Report::class);
    }

    public function bookmarks(): HasMany
    {
        return $this->hasMany(Bookmark::class);
    }

    public function bookmarkedSuggestions(): BelongsToMany
    {
        return $this->belongsToMany(Suggestion::class, 'bookmarks')
            ->withTimestamps();
    }

    public function isGuestAccount(): bool
    {
        return filled($this->device_id) && str_ends_with($this->email, '@guest.neyapsam.local');
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return $this->is_admin && ! $this->is_banned;
    }
}
