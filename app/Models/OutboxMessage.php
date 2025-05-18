<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OutboxMessage extends Model
{
    public const STATUS_PENDING = 'pending';
    public const STATUS_PROCESSED = 'processed';
    public const STATUS_FAILED = 'failed';

    protected $fillable = [
        'event_id',
        'topic',
        'payload',
        'headers',
        'status',
        'sent_at',
    ];

    protected $casts = [
        'payload' => 'array',
        'headers' => 'array',
        'sent_at' => 'datetime',
    ];

    public function toProduce(): array {
        return [
            'topic'   => $this->topic,
            'payload' => $this->payload,
            'key'     => $this->event_id,
            'headers' => $this->headers,
        ];
    }
}
