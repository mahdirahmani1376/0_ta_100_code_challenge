<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class BankAccount
 * @package App\Models
 *
 * @property int id
 * @property Carbon created_at
 * @property Carbon updated_at
 * @property Carbon deleted_at
 * @property string title
 * @property int display_order
 * @property string sheba_number
 * @property string account_number
 * @property string card_number
 * @property int rahkaran_id
 */
class BankAccount extends Model
{
    use HasFactory;
    use SoftDeletes;
}
