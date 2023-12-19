<?php

namespace MakeIT\DiscreteApi\Organizations\Traits;

use App\Models\User;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
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

    public function role(): Attribute
    {
        return Attribute::get(function (): ?string {
            if (isset($this->pivot->role)) {
                return config('discreteapiorganizations.roles')[$this->pivot->role];
            } else {
                if (request()->user() instanceof User || request()->user() instanceof Authenticatable) {
                    $tmp = request()->user()->organizations()->select(['id', 'organization_id', 'user_id'])->where('user_id', $this->id)->first();
                    if (!is_null($tmp)) {
                        return $tmp->role;
                    }
                } elseif ($this->is_personal) {
                    // patch if created for now via observer (?no pivot data at this time)
                    return config('discreteapiorganizations.roles')[1];
                }
            }
            return null;
        });
    }
}
