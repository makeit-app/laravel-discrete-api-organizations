<?php

namespace MakeIT\DiscreteApi\Organizations\Traits;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use MakeIT\DiscreteApi\Organizations\Models\Organization;

trait HasOrganization
{
    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class, 'organization_id');
    }
}
