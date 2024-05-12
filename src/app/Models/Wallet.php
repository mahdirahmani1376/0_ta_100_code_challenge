<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Wallet
 * @package App\Models
 *
 * @property int id
 * @property Carbon created_at
 * @property Carbon updated_at
 * @property Carbon deleted_at
 * @property int profile_id
 * @property string name
 * @property float balance
 * @property boolean is_active
 *
 * @property Collection creditTransactions
 */
class Wallet extends Model
{
    use SoftDeletes;

    const WALLET_DEFAULT_NAME = 'client';

    protected $fillable = [
        'profile_id',
        'name',
        'balance',
        'is_active',
    ];

    public function creditTransactions(): HasMany
    {
        return $this->hasMany(CreditTransaction::class);
    }
}
