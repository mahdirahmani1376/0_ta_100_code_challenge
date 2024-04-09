<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int id
 * @property int client_id
 * @property int rahkaran_id
 */
class Profile extends Model
{
    protected $fillable = [
        'client_id',
        'rahkaran_id',
    ];
}
