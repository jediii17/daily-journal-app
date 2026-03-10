<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Entry extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'date',
        'body',
    ];
    protected $casts = [
        'date' => 'date',
    ];

    /**
     * Get the user that owns the entry.
     *
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }


    public function scopeFilter(Builder $query, array $filters): void
    {
        $query->when(
            $filters['search'] ?? null,
            function (Builder $q, $search) {
                $q->whereAny(['title', 'body'], 'like', "%{$search}%");
            }
        );
    }
}
