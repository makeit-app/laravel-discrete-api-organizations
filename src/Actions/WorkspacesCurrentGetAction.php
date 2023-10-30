<?php

namespace MakeIT\DiscreteApi\Organizations\Actions;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use MakeIT\DiscreteApi\Organizations\Contracts\WorkspacesCurrentGetContract;

class WorkspacesCurrentGetAction extends WorkspacesCurrentGetContract
{
    public function handle(User $User): ?JsonResponse
    {
        if (!app()->runningInConsole()) {
            if (!is_null($User->profile)) {
                $User->profile->load(['organization.workspaces', 'workspace']);
                if (!is_null($User->profile->workspace)) {
                    return response()->json($User->profile->workspace->toArray());
                } else {
                    return response()->json([]);
                }
            } else {
                return response()->json(['errors' => __('User account without profiloe')], 404);
            }
        }
        return null;
    }
}
