<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int id
 * @property int client_id
 * @property int rahkaran_id
 * @property HasMany invoices
 */
class Profile extends Model
{
    protected $fillable = [
        'id',
        'client_id',
        'rahkaran_id',
        'created_at',
        'updated_at'
    ];

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class, 'profile_id');
    }
}
