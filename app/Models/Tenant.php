<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tenant extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'api_token',
        'meilisearch_index',
        'vector_namespace',
        'user_id',
        'active',
    ];

    /**
     * Get the feeds for the tenant.
     */
    public function feeds()
    {
        return $this->hasMany(Feed::class);
    }

    /**
     * Get the user that owns the tenant.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Generate a new API token for the tenant.
     */
    public function generateApiToken()
    {
        $this->update([
            'api_token' => bin2hex(random_bytes(32)),
        ]);

        return $this->api_token;
    }
}
