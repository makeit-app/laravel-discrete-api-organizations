<?php

namespace MakeIT\DiscreteApi\Organizations\Actions;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Gate;
use MakeIT\DiscreteApi\Organizations\Contracts\MembersListContract;

class MembersListAction extends MembersListContract
{
    public function handle(User $User): ?JsonResponse
    {
        if (!app()->runningInConsole()) {
            $User->load(['organizations']);
            $User->profile->load([
                'organization' => function ($q) {
                    return $q->select(['id', 'title', 'is_personal']);
                },
            ]);
            $Organization = $User->profile->organization;
            if (!is_null($Organization)) {
                Gate::forUser($User)->authorize('view', $Organization);
                $Organization->load([
                    'members' => function ($q) {
                        return $q->with([
                            'profile' => function ($q) {
                                return $q->select(['user_id', 'lastname', 'firstname', 'organization_id', 'workspace_id'])->with([
                                    'organization' => function ($q) {
                                        return $q->select(['id', 'title', 'is_personal']);
                                    },
                                    'workspace' => function ($q) {
                                        return $q->select(['id', 'title', 'is_default']);
                                    }
                                ]);
                            }
                        ]);
                    }
                ]);
                return response()->json($Organization->members);
            } else {
                return response()->json(null, 404);
            }
        }
        return null;
    }
}
