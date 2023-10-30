<?php

namespace MakeIT\DiscreteApi\Organizations\Actions;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use MakeIT\DiscreteApi\Organizations\Contracts\OrganizationsListContract;

class OrganizationsListAction extends OrganizationsListContract
{
    public function handle(User $User): ?JsonResponse
    {
        if (!app()->runningInConsole()) {
            $User->load([
                'organizations' => function ($q) {
                    return $q->ordered()->with([
                        'workspaces' => function ($q) {
                            return $q->ordered();
                        }
                    ]);
                },
                'profile.organization.workspaces',
                'profile.workspace',
            ]);
            $Organization = $User->profile->organization;
            $Workspace = $User->profile->workspace;
            $User->organizations->each(function (&$org) use ($Organization, $Workspace) {
                $org->is_current = $org->id === $Organization->id;
                $org->workspaces->each(function (&$space) use ($Workspace) {
                    $space->is_current = $space->id === $Workspace->id;
                });
            });
            return response()->json($User->organizations->toArray());
        }
        return null;
    }
}
