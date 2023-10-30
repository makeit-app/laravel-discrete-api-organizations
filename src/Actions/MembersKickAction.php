<?php

namespace MakeIT\DiscreteApi\Organizations\Actions;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Gate;
use MakeIT\DiscreteApi\Organizations\Contracts\MembersKickContract;

class MembersKickAction extends MembersKickContract
{
    public function handle(User $User, User $Member): ?JsonResponse
    {
        if (!app()->runningInConsole()) {
            // load request user data
            $User->load([
                'profile' => function ($q) {
                    return $q->with([
                        'organization' => function ($q) {
                            return $q->with([
                                'slots',
                                'workspaces' => function ($q) {
                                    return $q->ordered();
                                },
                                'members',
                            ]);
                        },
                        'workspace',
                    ]);
                },
                'organization_slots',
                'organizations' => function ($q) {
                    return $q->ordered()->with([
                        'slots',
                        'workspaces' => function ($q) {
                            return $q->ordered();
                        },
                        'members',
                    ]);
                },
            ]);
            $Organization = $User->profile->organization;
            if (is_null($Organization)) {
                return response()->json(['message' => __('You do not have an active organization on your profile. Nothing to work with'), 'errors' => __('You do not have an active organization on your profile. Nothing to work with')], 404);
            }
            // load member data
            $Member->load([
                'profile' => function ($q) {
                    return $q->with([
                        'organization' => function ($q) {
                            return $q->with([
                                'slots',
                                'workspaces' => function ($q) {
                                    return $q->ordered();
                                },
                                'members',
                            ]);
                        },
                        'workspace',
                    ]);
                },
                'organization_slots',
                'organizations' => function ($q) {
                    return $q->ordered()->with([
                        'slots',
                        'workspaces' => function ($q) {
                            return $q->ordered();
                        },
                        'members',
                    ]);
                },
            ]);
            // check for organization is in user's org list (in irg)
            if (!$Member->organizations->find($Organization->id)) {
                return response()->json(['message' => __('Membership\'s organization not found'), 'errors' => ['invite' => __('Membership\'s organization not found')]], 404);
            }
            // load all related organization's data
            $Organization->load(['members', 'slots']);
            // check for owner/admin role
            Gate::forUser($User)->authorize('update', $Organization);
            // check for membership/member
            Gate::forUser($Member)->authorize('view', $Organization);
            // double-check that the participant is a member of the organization
            if (!$Organization->members->where('user_id', $Member->id)->first()) {
                return response()->json(['message' => __('Membership is not found'), 'errors' => ['invite' => __('Membership is not found')]], 404);
            }
            // remove membership
            $Organization->members()->detach($Member);

            return response()->json(null, 204);
        }

        return null;
    }
}
