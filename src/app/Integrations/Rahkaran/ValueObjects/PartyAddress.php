<?php

namespace App\Integrations\Rahkaran\ValueObjects;

use Illuminate\Database\Eloquent\Model;

/**
 * Class PartyAddress
 * @package App\ValueObjects\Rahkaran
 * @property $Details
 * @property $Details_En
 * @property $Email
 * @property $Fax
 * @property $ID
 * @property $IsMainAddress
 * @property $Name
 * @property $Phone
 * @property $RegionalDivisionRef
 * @property $WebPage
 * @property $ZipCode
 */
class PartyAddress extends Model
{
    /**
     * @var string[]
     */
    protected $fillable = [
        'Details',
        'Details_En',
        'Email',
        'Fax',
        'ID',
        'IsMainAddress',
        'Name',
        'Phone',
        'RegionalDivisionRef',
        'WebPage',
        'ZipCode',
    ];
}
