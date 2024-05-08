<?php

namespace App\Integrations\Rahkaran\ValueObjects;

use Illuminate\Support\Collection;

/**
 * Class Product
 * @property int $id
 * @property string $name
 * @property string $description
 * @property int $base_price
 * @property int $whmcs_id
 * @property string $type
 * @property-read array $billing_cycles
 * @property int $monthly
 * @property int $quarterly
 * @property int $semiannually
 * @property int $annually
 * @property int $biennially
 * @property int $triennially
 * @property int $product_group_id
 * @property bool $active
 * @property bool $featured
 * @property bool $has_domain
 * @property int $order
 * @property string $module_name
 * @property-read  string $slug
 * @property ProductGroup $productGroup
 */
class Product extends \stdClass
{

}
