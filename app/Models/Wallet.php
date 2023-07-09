<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Wallet
 * @package App\Models
 *
 * @property int id
 * @property Carbon created_at
 * @property Carbon updated_at
 * @property Carbon deleted_at
 * @property int client_id
 * @property string name
 * @property float balance
 * @property boolean is_active
 */
class Wallet extends Model
{
    use HasFactory;
    use SoftDeletes;
}
