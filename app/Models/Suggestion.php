<?php

namespace App\Models;

use App\Enums\SuggestionStatus;
use App\Enums\VoteType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Suggestion extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'subcategory_id',
        'user_id',
        'title',
        'description',
        'upvote_count',
        'downvote_count',
        'net_score',
        'status',
        'is_featured',
    ];

    protected function casts(): array
    {
        return [
            'upvote_count' => 'integer',
            'downvote_count' => 'integer',
            'net_score' => 'integer',
            'is_featured' => 'boolean',
            'status' => SuggestionStatus::class,
        ];
    }

    public function scopeApproved(Builder $query): Builder
    {
        return $query->where('status', SuggestionStatus::Approved);
    }

    public function subcategory(): BelongsTo
    {
        return $this->belongsTo(Subcategory::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function votes(): HasMany
    {
        return $this->hasMany(Vote::class);
    }

    public function reports(): HasMany
    {
        return $this->hasMany(Report::class);
    }

    public function refreshVoteStats(): void
    {
        $upvotes = $this->votes()->where('type', VoteType::Up)->count();
        $downvotes = $this->votes()->where('type', VoteType::Down)->count();

        $this->forceFill([
            'upvote_count' => $upvotes,
            'downvote_count' => $downvotes,
            'net_score' => $upvotes - $downvotes,
        ])->save();
    }
}
