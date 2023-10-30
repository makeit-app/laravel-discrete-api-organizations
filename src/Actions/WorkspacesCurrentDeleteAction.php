<?php

namespace MakeIT\DiscreteApi\Organizations\Actions;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Gate;
use MakeIT\DiscreteApi\Organizations\Contracts\WorkspacesCurrentDeleteContract;
use MakeIT\DiscreteApi\Organizations\Helpers\DiscreteApiOrganizationsHelper;

class WorkspacesCurrentDeleteAction extends WorkspacesCurrentDeleteContract
{
    public function handle(User $User): ?JsonResponse
    {
        if (!app()->runningInConsole()) {
            if (!is_null($User->profile)) {
                $User->profile->load(['organization.workspaces', 'workspace']);
                if (!is_null($User->profile->organization) && $User->profile->organization->is_personal === true && $User->profile->workspace->is_default === true) {
                    return response()->json([
                        'message' => __('Unable to remove default workspace of personal organization'),
                        'errors' => [
                            'organization' => __('Unable to remove default workspace of personal organization'),
                        ]
                    ], 403);
                }
                Gate::forUser($User)->authorize('delete', $User->profile->workspace);
                $User->profile->workspace->delete();
                DiscreteApiOrganizationsHelper::switchTo($User, $User->profile->organization);
                return response()->json(null, 204);
            }
            return response()->json(null, 404);
        }
        return null;
    }
}
