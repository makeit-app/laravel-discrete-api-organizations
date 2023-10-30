<?php

namespace MakeIT\DiscreteApi\Organizations\Actions;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Gate;
use MakeIT\DiscreteApi\Organizations\Contracts\OrganizationsCurrentDeleteContract;
use MakeIT\DiscreteApi\Organizations\Helpers\DiscreteApiOrganizationsHelper;

class OrganizationsCurrentDeleteAction extends OrganizationsCurrentDeleteContract
{
    public function handle(User $User): ?JsonResponse
    {
        if (!app()->runningInConsole()) {
            if (!is_null($User->profile)) {
                $User->profile->load(['organization.workspaces', 'workspace']);
                if (!is_null($User->profile->organization) && $User->profile->organization->is_personal === true) {
                    return response()->json([
                        'message' => __('Unable to remove personal Organization'),
                        'errors' => [
                            'organization' => __('Unable to remove personal Organization'),
                        ]
                    ], 403);
                }
                Gate::forUser($User)->authorize('delete', $User->profile->organization);
                $User->profile->organization->delete();
                DiscreteApiOrganizationsHelper::switchTo($User);
                return response()->json(null, 204);
            }
            return response()->json(null, 404);
        }
        return null;
    }
}
