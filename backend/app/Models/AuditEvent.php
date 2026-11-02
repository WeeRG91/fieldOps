<?php

namespace App\Models;

use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $event
 * @property string $actor_type
 * @property int|null $actor_id
 * @property int|null $subject_id
 * @property CarbonImmutable $occurred_at
 * @property array<string, mixed>|null $metadata
 */
#[Fillable([
    'event',
    'actor_type',
    'actor_id',
    'subject_id',
    'occurred_at',
    'metadata',
])]
class AuditEvent extends Model
{
    public $timestamps = false;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'occurred_at' => 'immutable_datetime',
            'metadata' => 'array',
        ];
    }
}
