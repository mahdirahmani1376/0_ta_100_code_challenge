<?php

namespace App\Integrations\Rahkaran\ValueObjects;
use stdClass;

/**
 * Class Client
 * @package App\Entities
 * @property int $id
 * @property int $whmcs_id
 * @property int cloud_profile_id
 * @property int $owner_user_id
 * @property string $status
 * @property string $email
 * @property string $first_name
 * @property string $last_name
 * @property string $mobile_number
 * @property string $national_code
 * @property string $company_name
 * @property string $company_national_code
 * @property string $company_registered_number
 * @property string $company_phone_number
 * @property string $company_state
 * @property string $company_city
 * @property string $company_address
 * @property string $company_postal_code
 * @property string $country
 * @property string $state
 * @property string $city
 * @property string $address
 * @property string $postal_code
 * @property string $phone_number
 * @property string $identity_number
 * @property string $company_type
 * @property string $full_name
 * @property bool $ab_testing
 * @property bool $is_foreigner
 * @property Carbon $birth_date
 * @property string $birth_location
 * @property string $parent_name
 * @property array $group
 * @property bool $is_legal
 * @property int $rahkaran_id
 * @property string|int $cloud_wallet
 * @property int $vm_limit
 * @property WhmcsClient $whmcs
 * @property User[]|Collection $users
 * @property User|null $user
 * @property Cart $cart
 * @property Order[]|Collection $orders
 * @property ProviderToken|null $providerTokens
 * @property HasMany vdis
 * @property HasMany notes
 * @property string $derak_id
 * @property string $domain_profile_id
 * @property  HasMany $domains
 * @property Collection $tags
 * @property  AccessToken accessToken
 * @property  string technical_contact
 * @property  string technical_fullname
 * @property  string technical_contact_2
 * @property  string technical_fullname_2
 * @property  int ticket_profile_id
 * @property  int product_profile_id
 **/
class Client extends stdClass
{

}
