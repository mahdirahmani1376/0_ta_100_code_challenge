<?php

namespace App\Repositories\ClientBankAccount;

use App\Models\ClientBankAccount;
use App\Repositories\Base\BaseRepository;
use App\Repositories\ClientBankAccount\Interface\ClientBankAccountRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class ClientBankAccountRepository extends BaseRepository implements ClientBankAccountRepositoryInterface
{
    public string $model = ClientBankAccount::class;

    public function index(array $data): Collection|LengthAwarePaginator
    {
        $query = self::newQuery();
        if (!empty($data['search'])) {
            $query->where(function (Builder $query) use ($data) {
                $query->where('card_number', 'LIKE', '%' . $data['search'] . '%')
                    ->orWhere('sheba_number', 'LIKE', '%' . $data['search'] . '%')
                    ->orWhere('account_number', 'LIKE', '%' . $data['search'] . '%')
                    ->orWhere('bank_name', 'LIKE', '%' . $data['search'] . '%')
                    ->orWhere('owner_name', 'LIKE', '%' . $data['search'] . '%');
            });
        }
        if (!empty($data['status'])) {
            $query->where('status', '=', $data['status']);
        }
        if (!empty($data['profile_id'])) {
            $query->where('profile_id', $data['profile_id']);
        }
        if (isset($data['export']) && $data['export']) {
            return parent::sortQuery($query)->get();
        }

        return self::paginate($query);
    }

    public function findSimilarWithZarinpalId(ClientBankAccount $clientBankAccount)
    {
        return self::newQuery()
            ->where('profile_id', $clientBankAccount->profile_id)
            ->where(function (Builder $query) use ($clientBankAccount) {
                $query->where('sheba_number', $clientBankAccount->sheba_number);
                $query->orWhere('card_number', $clientBankAccount->card_number);
            })
            ->whereNotNull('zarinpal_bank_account_id')
            ->where('id', '<>', $clientBankAccount->id)
            ->first();
    }
}
