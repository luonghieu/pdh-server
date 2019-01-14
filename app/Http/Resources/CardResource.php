<?php

namespace App\Http\Resources;

use App\Traits\ResourceResponse;
use Illuminate\Http\Resources\Json\Resource;

class CardResource extends Resource
{
    use ResourceResponse;

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return $this->filterNull([
            'id' => null,
            'card_id' => 'card_xxx',
            'address_city' => null,
            'address_country' => null,
            'address_line1' => null,
            'address_line1_check' => null,
            'address_line2' => null,
            'address_state' => null,
            'address_zip' => null,
            'address_zip_check' => null,
            'brand' => 'VISA',
            'country' => 'US',
            'customer' => 'cus_xxx',
            'cvc_check' => null,
            'dynamic_last4' => null,
            'exp_month' => 12,
            'exp_year' => 2030,
            'fingerprint' => 'xxx',
            'funding' => 'credit',
            'last4' => 4444,
            'name' => null,
            'tokenization_method' => null,
            'is_default' => 1,
            'is_expired' => 0,
        ]);
    }
}
