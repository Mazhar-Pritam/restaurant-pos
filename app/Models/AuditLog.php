<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'user_id', 'action', 'auditable_type', 'auditable_id',
        'old_values', 'new_values', 'reason', 'ip_address', 'created_at',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
        'created_at' => 'datetime',
    ];

    /**
     * Write one audit entry. Example:
     * AuditLog::record('refund.approved', $refund, reason: 'Wrong item');
     */
    public static function record(
        string $action,
        ?Model $subject = null,
        array $old = [],
        array $new = [],
        ?string $reason = null
    ): self {
        return self::create([
            'user_id'        => auth()->id(),
            'action'         => $action,
            'auditable_type' => $subject?->getMorphClass(),
            'auditable_id'   => $subject?->getKey(),
            'old_values'     => $old ?: null,
            'new_values'     => $new ?: null,
            'reason'         => $reason,
            'ip_address'     => request()->ip(),
            'created_at'     => now(),
        ]);
    }
}
