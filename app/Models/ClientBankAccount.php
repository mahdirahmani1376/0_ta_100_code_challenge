<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class ClientBankAccount
 * @package App\Models
 *
 * @property int id
 * @property Carbon created_at
 * @property Carbon updated_at
 * @property Carbon deleted_at
 * @property int client_id
 * @property int zarinpal_bank_account_id
 * @property string bank_name
 * @property string owner_name
 * @property string sheba_number
 * @property string account_number
 * @property string card_number
 */
class ClientBankAccount extends Model
{
    use HasFactory;
    use SoftDeletes;

    const STATUS_ACTIVE = 'active';
    const STATUS_PENDING = 'pending';
    const STATUS_REJECTED = 'rejected';
}
