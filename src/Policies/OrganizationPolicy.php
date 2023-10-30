<?php

namespace MakeIT\DiscreteApi\Organizations\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use MakeIT\DiscreteApi\Organizations\Helpers\DiscreteApiOrganizationsHelper;
use MakeIT\DiscreteApi\Organizations\Models\Organization as Model;

class OrganizationPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param User $User
     * @return bool
     */
    public function viewAny(User $User): bool
    {
        return $User->hasRole(['super', 'admin', 'support']);
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param User $User
     * @param Model $Model
     * @return bool
     */
    public function view(User $User, Model $Model): bool
    {
        return $User->hasRole(['super', 'admin', 'support']) || DiscreteApiOrganizationsHelper::can_read_organization($User, $Model);
    }

    /**
     * Determine whether the user can create models.
     *
     * @return bool
     */
    public function create(): bool
    {
        return true;
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param User $User
     * @param Model $Model
     * @return bool
     */
    public function update(User $User, Model $Model): bool
    {
        return $User->hasRole(['super', 'admin', 'support']) || (
            DiscreteApiOrganizationsHelper::is_member($User, $Model) &&
                DiscreteApiOrganizationsHelper::can_write_organization($User, $Model)
        );
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param User $User
     * @param Model $Model
     * @return bool
     */
    public function delete(User $User, Model $Model): bool
    {
        return $User->hasRole(['super', 'admin', 'support']) || (
            DiscreteApiOrganizationsHelper::is_member($User, $Model) &&
                DiscreteApiOrganizationsHelper::can_delete_organization($User, $Model)
        );
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param User $User
     * @param Model $Model
     * @return bool
     */
    public function restore(User $User, Model $Model): bool
    {
        return $User->hasRole(['super', 'admin', 'support']);
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param User $User
     * @param Model $Model
     * @return bool
     */
    public function forceDelete(User $User, Model $Model): bool
    {
        return false;
    }
}
