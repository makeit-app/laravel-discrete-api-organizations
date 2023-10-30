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
        return $this->belongsToMany(Organization::class, 'organizations_members')->withPivot(['role']);
    }

    public function role(): ?string
    {
        if (isset($this->pivot)) {
            return config('discreteapiorganizations.roles')[$this->pivot->role];
        }

        return null;
    }
}
