<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int id
 * @property int client_id
 */
class Profile extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
    ];
}
