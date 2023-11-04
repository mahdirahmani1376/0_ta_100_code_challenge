<?php

namespace App\Repositories\ClientBankAccount;

use App\Models\ClientBankAccount;
use App\Repositories\Base\BaseRepository;
use App\Repositories\ClientBankAccount\Interface\ClientBankAccountRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

class ClientBankAccountRepository extends BaseRepository implements ClientBankAccountRepositoryInterface
{
    public string $model = ClientBankAccount::class;

    public function adminIndex(array $data): LengthAwarePaginator
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
        if (!empty($data['client_id'])) {
            $query->where('client_id', $data['client_id']);
        }
        $query->orderBy(
            $data['sort'] ?? BaseRepository::DEFAULT_SORT_COLUMN,
            $data['sortDirection'] ?? BaseRepository::DEFAULT_SORT_COLUMN_DIRECTION,
        );

        return self::paginate($query);
    }

    public function profileIndex(int $clientId, array $data): LengthAwarePaginator
    {
        $data['client_id'] = $clientId;

        return self::adminIndex($data);
    }

    public function findSimilarWithZarinpalId(ClientBankAccount $clientBankAccount)
    {
        return self::newQuery()
            ->where('client_id', $clientBankAccount->client_id)
            ->where(function (Builder $query) use ($clientBankAccount) {
                $query->where('sheba_number', $clientBankAccount->sheba_number);
                $query->orWhere('card_number', $clientBankAccount->card_number);
            })
            ->whereNotNull('zarinpal_bank_account_id')
            ->where('id', '<>', $clientBankAccount->id)
            ->first();
    }
}
