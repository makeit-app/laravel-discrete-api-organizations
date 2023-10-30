<?php

/** @noinspection ALL */

namespace MakeIT\DiscreteApi\Organizations\Helpers;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Collection;
use MakeIT\DiscreteApi\Organizations\Models\Organization;
use MakeIT\DiscreteApi\Organizations\Models\Workspace;
use MakeIT\Utils\Sorter;

class DiscreteApiOrganizationsHelper
{
    public static function reorderOrganizationWorkspaces(Collection|EloquentCollection $Workspaces): mixed
    {
        if ($Workspaces->count() > 0) {
            $x = 1;
            $Workspaces->each(function (&$item) use ($x) {
                $item->{Sorter::FIELD} = $x;
                $x++;
            });
        }
        return $Workspaces;
    }

    public static function reorderUserOrganizations(Collection|EloquentCollection $Organizations): mixed
    {
        if ($Organizations->count() > 0) {
            $x = 1;
            $Organizations->each(function (&$item) use ($x) {
                $item->{Sorter::FIELD} = $x;
                $x++;
            });
        }
        return $Organizations;
    }

    public static function organizations_limit(): int
    {
        /**
         * TODO: Subscription
         * if have subscription and logged in - read limits from subscription
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
         * if have subscription and logged in - read limits from subscription
         */
        switch (config('app.env')) {
            case 'local':
                return 3;
            default:
            case 'production':
                return 1;
        }
    }

    public static function members_limit(): int
    {
        /**
         * TODO: Subscription
         * if have subscription and logged in - read limits from subscription
         */
        switch (config('app.env')) {
            case 'local':
                return 3;
            default:
            case 'production':
                return 2;
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

    public static function switchTo(User $User, Organization $Organization = null, Workspace $Workspace = null): void
    {
        if (!is_null($Organization) && static::is_member($User, $Organization)) {
            // switch to the specified org if user is member of it
            // set profile organization
            $User->profile->organization_id = $Organization->id;
            if ($Organization->workspaces->count() > 0) {
                if (!is_null($Workspace) && in_array($Workspace->id, $Organization->workspaces->pluck('id'))) {
                    // switch to the workspace of specified org
                    // set profile workspace
                    $User->profile->workspace_id = $Workspace->id;
                } else {
                    // this is workspace of another organization
                    // try to switch to its organization
                    static::switchTo($User, $Workspace->organization, $Workspace);
                }
            } else {
                // else no workspace - reset
                // actually both organization and workspace are always there,
                // but let's close the hole too
                $User->profile->workspace_id = null;
            }
        } else {
            // falloff to user's organizations list
            // find for proper org
            $Organizations = $User->organizations()->with(['workspaces'])->get();
            if ($Organizations->count() > 0) {
                // is there personal organization?
                if (!is_null($Organizations->where('is_personal', true)->first())) {
                    // set profile organization
                    $User->profile->organization_id = $Organizations->where('is_personal', true)->first()->id;
                    // extract the organization's workspaces
                    $Workspaces = $Organizations->where('is_personal', true)->first()->workspaces;
                } else {
                    // set profile organization
                    $User->profile->organization_id = $Organizations->first()->id;
                    // extract the organization's workspaces
                    $Workspaces = $Organizations->first()->workspaces;
                }
                if (!is_null($Workspace) && in_array($Workspace->id, $Workspaces->pluck('id'))) {
                    // we almost never get here, but we're gonna close this hole anyway
                    // because in 99.99% of cases it is a random organization from a list of organizations
                    // accordingly, the workspace is also random...
                    // switch to the workspace of previously choosed org
                    // set profile workspace
                    $User->profile->workspace_id = $Workspace->id;
                } elseif ($Workspaces->count() > 0) {
                    // is there default workspace?
                    if (!is_null($Workspaces->where('is_default', true)->first())) {
                        // set profile default organization's workspace
                        $User->profile->workspace_id = $Workspaces->where('is_default', true)->first()->id;
                    } else {
                        // set profile first found organization's workspace
                        $User->profile->workspace_id = $Workspaces->first()->id;
                    }
                } else {
                    // else no workspace - reset
                    // actually both organization and workspace are always there,
                    // but let's close the hole too
                    $User->profile->workspace_id = null;
                }
            } else {
                // actually both organization and workspace are always there,
                // but let's close the hole too
                $User->profile->forceFill([
                    'organization_id' => null,
                    'workspace_id' => null,
                ]);
            }
        }
        $User->profile->save();
        $User->profile->load(['organization.workspaces', 'workspace']);
    }
}
