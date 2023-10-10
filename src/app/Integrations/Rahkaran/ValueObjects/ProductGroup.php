<?php

namespace App\Integrations\Rahkaran\ValueObjects;
use Illuminate\Support\Collection;

/**
 * Class ProductGroup
 * @property int $id
 * @property string $name
 * @property string $title
 * @property string $slug
 * @property bool $active
 * @property int $whmcs_id
 * @property int $order
 * @property bool $upgradable
 * @property bool $has_unique_domain
 * @property Product[]|Collection $products
 */
class ProductGroup extends \stdClass
{

}
