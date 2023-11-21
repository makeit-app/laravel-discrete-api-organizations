<?php

namespace MakeIT\DiscreteApi\Organizations\Helpers;

use App\Models\User;
use MakeIT\DiscreteApi\Organizations\Models\Organization;

class DiscreteApiOrganizationsHelper
{
    public static function organizatios_limit(): int
    {
        /**
         * TODO: Subscription
         * - read limits from subscription
         */
        switch (config('app.env')) {
            case 'local':
                return 2;
            default:
            case 'production':
                return 1;
        }
    }

    public static function workspaces_limit(): int
    {
        /**
         * TODO: Subscription
         * - read limits from subscription
         */
        switch (config('app.env')) {
            case 'local':
                return 3;
            default:
            case 'production':
                return 1;
        }
    }

    public static function can_read_organization(User $User, Organization $Organization): bool
    {
        return static::is_member($User, $Organization);
    }

    public static function is_member(User $User, Organization $Organization): bool
    {
        return (bool)$User->organizations()->where('organization_id', $Organization->id)->count();
    }

    public static function can_write_organization(User $User, Organization $Organization): bool
    {
        $tmp = $User->organizations()->where('organization_id', $Organization->id)->first();
        if (!is_null($tmp)) {
            return in_array($tmp->role_id, [1, 2]); // super and admin
        }
        return false;
    }

    public static function can_delete_organization(User $User, Organization $Organization): bool
    {
        $tmp = $User->organizations()->where('organization_id', $Organization->id)->first();
        if (!is_null($tmp) && !$tmp->is_personal) {
            return $tmp->role_id == 1; // super only
        }
        return false;
    }

    public static function updateProfileOrganization(User $User): void
    {
        $Organizations = $User->organizations()->get();
        if ($Organizations->count() > 0) {
            $User->profile->forceFill(['organization_id' => $Organizations->first()->id])->save();
        } elseif ($Organizations->count() > 1) {
            if (!is_null($Organizations->where('is_personal', true)->first())) {
                $User->profile->forceFill(['organization_id' => $Organizations->where('is_personal', true)->first()->id])->save();
            } else {
                $User->profile->forceFill(['organization_id' => $Organizations->first()->id])->save();
            }
        } else {
            $User->profile->forceFill(['organization_id' => null])->save();
        }
        $User->profile->load(['organization.workspaces']);
    }

    public static function updateProfileWorkspace(User $User): void
    {
        if (!is_null($User->profile->organization) && $User->profile->organization->workspaces->count() > 0) {
            $Workspaces = $User->profile->organization->workspaces;
            if (!is_null($Workspaces->where('is_default', true)->first())) {
                $User->profile->forceFill(['workspace_id' => $Workspaces->where('is_default', true)->first()->id])->save();
            } else {
                $User->profile->forceFill(['workspace_id' => $Workspaces->first()->id])->save();
            }
        }
        $User->profile->forceFill(['workspace_id' => null])->save();
        $User->profile->load(['workspace']);
    }
}
