<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToTenant;

class Feed extends Model
{
    use HasFactory, BelongsToTenant;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'tenant_id',
        'url',
        'format',
        'sync_schedule',
        'last_sync_at',
        'next_sync_at',
        'active',
        'sync_status',
        'error_log',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'last_sync_at' => 'datetime',
        'next_sync_at' => 'datetime',
        'sync_status' => 'json',
        'active' => 'boolean',
    ];

    /**
     * Get the tenant that owns the feed.
     */
    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Calculate the next sync time based on the schedule.
     */
    public function calculateNextSync()
    {
        $now = now();

        switch ($this->sync_schedule) {
            case 'hourly':
                return $now->addHour();
            case 'daily':
                return $now->addDay();
            case 'weekly':
                return $now->addWeek();
            default:
                return $now->addDay();
        }
    }
}
