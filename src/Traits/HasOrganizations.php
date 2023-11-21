<?php

namespace MakeIT\DiscreteApi\Organizations\Traits;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use MakeIT\DiscreteApi\Organizations\Models\Organization;

/**
 * @method belongsToMany(string $class, string $string)
 */
trait HasOrganizations
{
    public function organizations(): BelongsToMany
    {
        return $this->belongsToMany(Organization::class, 'pivot_organizations_users')->withPivot(['role']);
    }
}
