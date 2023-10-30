<?php

namespace MakeIT\DiscreteApi\Organizations\Actions;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Gate;
use MakeIT\DiscreteApi\Organizations\Contracts\OrganizationsSwitchContract;
use MakeIT\DiscreteApi\Organizations\Models\Organization;

class OrganizationsSwitchAction extends OrganizationsSwitchContract
{
    public function handle(User $User, Organization $Organization): ?JsonResponse
    {
        if (!app()->runningInConsole()) {
            if (!is_null($User->profile)) {
                $User->profile->load(['organization.workspaces', 'workspace']);
                Gate::forUser($User)->authorize('view', $Organization);
                $Organization->load(['workspaces']);
                if ($Organization->workspaces->count() > 0) {
                    $DefaultWorkspace = $Organization->workspaces->where('is_default', true)->first();
                    if (!is_null($DefaultWorkspace)) {
                        $User->profile->workspace_id = $DefaultWorkspace->id;
                    } else {
                        $User->profile->workspace_id = $Organization->workspaces->first()->id;
                    }
                } else {
                    $User->profile->workspace_id = null;
                }
                $User->profile->organization_id = $Organization->id;
                $User->profile->save();
                return response()->json(null, 204);
            }
            return response()->json(null, 404);
        }
        return null;
    }
}
