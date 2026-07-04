<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SystemLog extends Model
{
    protected $fillable = [
        'user_id', 'action', 'module', 'record_type', 'record_id', 'description', 'ip_address',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public static function record(string $action, string $module, string $description = '', $record = null): void
    {
        $user = auth()->user();
        static::create([
            'user_id'     => $user?->id,
            'action'      => $action,
            'module'      => $module,
            'record_type' => $record ? class_basename($record) : null,
            'record_id'   => $record?->id ?? null,
            'description' => $description,
            'ip_address'  => request()->ip(),
        ]);
    }
}
