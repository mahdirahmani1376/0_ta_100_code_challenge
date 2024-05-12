<?php

namespace App\Integrations\Rahkaran\ValueObjects;

use Illuminate\Database\Eloquent\Model;

/**
 * Class DlObject
 * @package App\ValueObjects\Rahkaran
 * @property $Code
 * @property $DLTypeRef
 * @property $Description
 * @property $ID
 * @property $ReferenceID
 * @property $Title
 * @property $Title_En
 */
class DlObject extends Model
{
    /**
     * @var string[]
     */
    protected $fillable = [
        'Code',
        'DLTypeRef',
        'Description',
        'ID',
        'ReferenceID',
        'Title',
        'Title_En',
    ];
}
