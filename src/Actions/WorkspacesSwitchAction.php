<?php

namespace MakeIT\DiscreteApi\Organizations\Actions;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Gate;
use MakeIT\DiscreteApi\Organizations\Contracts\WorkspacesSwitchContract;
use MakeIT\DiscreteApi\Organizations\Helpers\DiscreteApiOrganizationsHelper;
use MakeIT\DiscreteApi\Organizations\Models\Workspace;

class WorkspacesSwitchAction extends WorkspacesSwitchContract
{
    public function handle(User $User, Workspace $Workspace): ?JsonResponse
    {
        if (!app()->runningInConsole()) {
            $Organization = $Workspace->organization;
            Gate::forUser($User)->authorize('view', $Organization);
            if (!is_null($Organization)) {
                if ($User->profile->organization->id !== $Organization->id) {
                    DiscreteApiOrganizationsHelper::switchTo($User, $Organization, $Workspace);
                    return response()->json(null, 204);
                }
            } else {
                return response()->json(null, 404);
            }
            // check if user in organization
            // if yes - switch => update profile
            // if no - abort
        }
        return null;
    }
}
