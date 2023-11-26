<?php

namespace MakeIT\DiscreteApi\Organizations\Actions;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use MakeIT\DiscreteApi\Organizations\Contracts\WorkspacesSwitchContract;
use MakeIT\DiscreteApi\Organizations\Models\Organization;

class WorkspacesSwitchAction extends WorkspacesSwitchContract
{
    public function handle(User $User, Workspaces $Workspace): ?JsonResponse
    {
        if (!app()->runningInConsole()) {
            // check if user in organization
            // if yes - switch => update profile
            // if no - abort
        }
        return null;
    }
}
