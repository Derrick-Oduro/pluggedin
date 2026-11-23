<?php

namespace App\Support;

use App\Models\AuditLog;
use Illuminate\Support\Str;

/**
 * Turns audit entries like "superadmin.user.updated" into readable text:
 * "updated user ama@example.com".
 */
class AuditDescriber
{
    /** Words for the last part of an action name. */
    private const VERBS = [
        'created' => 'added', 'updated' => 'updated', 'deleted' => 'deleted', 'restored' => 'restored',
        'made_live' => 'made live', 'moderated' => 'moderated', 'reported' => 'reported',
        'approved' => 'approved', 'suspended' => 'suspended', 'quoted' => 'sent a quote for',
        'completed' => 'completed', 'verified' => 'verified', 'unverified' => 'removed verification from',
        'resolved' => 'resolved', 'dismissed' => 'dismissed', 'removed' => 'took down', 'handled' => 'handled',
        'reopened' => 'reopened', 'reviewing' => 'started reviewing',
    ];

    /** Meta keys worth naming in the sentence, in order of preference. */
    private const LABEL_KEYS = ['name', 'email', 'title', 'product', 'device', 'subject'];

    public static function area(AuditLog $log): string
    {
        $parts = explode('.', $log->action);

        return count($parts) >= 3 ? $parts[1] : $parts[0];
    }

    public static function sentence(AuditLog $log): string
    {
        $parts = explode('.', $log->action);
        $verbKey = end($parts);
        $verb = self::VERBS[$verbKey] ?? str_replace('_', ' ', $verbKey);
        $thing = str_replace('_', ' ', self::area($log));

        $meta = (array) $log->meta;
        $label = collect(self::LABEL_KEYS)->map(fn ($key) => $meta[$key] ?? null)->first(fn ($value) => is_scalar($value) && $value !== '');
        $label = $label !== null ? '“'.Str::limit((string) $label, 60).'”' : ($log->entity_id ? '#'.$log->entity_id : '');

        return trim("{$verb} {$thing} {$label}");
    }

    /** Remaining meta as "Key: value" pairs for the detail column. */
    public static function details(AuditLog $log): array
    {
        return collect((array) $log->meta)
            ->reject(fn ($value) => $value === null || $value === '' || $value === [])
            ->mapWithKeys(fn ($value, $key) => [Str::headline((string) $key) => is_scalar($value) ? (string) $value : json_encode($value)])
            ->all();
    }
}
