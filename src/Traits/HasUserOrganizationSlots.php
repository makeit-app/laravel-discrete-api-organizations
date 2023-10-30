<?php

namespace MakeIT\DiscreteApi\Organizations\Traits;

use Illuminate\Database\Eloquent\Relations\HasOne;
use MakeIT\DiscreteApi\Organizations\Models\UserOrganizationSlot;

/**
 * @method hasOne(string $class, string $string)
 */
trait HasUserOrganizationSlots
{
    public function organization_slots(): HasOne
    {
        return $this->hasOne(UserOrganizationSlot::class, 'user_id');
    }
}
