<?php

namespace App\Integrations\Rahkaran\ValueObjects;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Party
 * @package App\ValueObjects\Rahkaran
 * @property $Alias
 * @property $CompanyName
 * @property $Company_EN
 * @property $EconomicCode
 * @property $FirstName
 * @property $FirstName_EN
 * @property $Gender
 * @property $ID
 * @property $LastName
 * @property $LastName_EN
 * @property $NationalID
 * @property $Type
 * @property $Title
 * @property Collection|PartyAddress[] $PartyAddresses
 */
class Party extends Model
{
    /**
     * @var string[]
     */
    protected $fillable = [
        'Alias',
        'CompanyName',
        'Company_EN',
        'EconomicCode',
        'FirstName',
        'FirstName_EN',
        'Gender',
        'ID',
        'LastName',
        'LastName_EN',
        'NationalID',
        'Type',
        'Title',
    ];

    /**
     * Gets Party Addresses as collection
     *
     * @return Collection|array|null
     */
    public function getPartyAddressesAttribute()
    {
        return $this->attributes['PartyAddresses'];
    }

    /**
     * Sets party addresses
     *
     * @param $party_addresses
     * @return $this
     */
    public function setPartyAddressesAttribute($party_addresses): Party
    {
        $this->attributes['PartyAddresses'] = $party_addresses instanceof Collection ? $party_addresses : (new Collection($party_addresses))->map(function ($party_address) {
            return $party_address instanceof PartyAddress ? $party_address : new PartyAddress($party_address);
        });

        return $this;
    }
}
