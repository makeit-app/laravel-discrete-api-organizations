<?php

namespace MakeIT\DiscreteApi\Organizations\Traits;

use Illuminate\Database\Eloquent\Relations\HasOne;
use MakeIT\DiscreteApi\Organizations\Models\OrganizationSlot;

/**
 * @method hasOne()
 */
trait HasOrganizationSlots
{
    public function organization_slots(): HasOne
    {
        return $this->hasOne(OrganizationSlot::class, 'user_id');
    }
}
