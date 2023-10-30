<?php

namespace MakeIT\DiscreteApi\Organizations\Actions;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Gate;
use MakeIT\DiscreteApi\Organizations\Contracts\MembersInviteDeclineContract;
use MakeIT\DiscreteApi\Organizations\Models\Organization;

class MembersInviteDeclineAction extends MembersInviteDeclineContract
{
    public function handle(User $User, User $Member, Organization $Organization): ?JsonResponse
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
                return response()->json(['message' => __('Invitation organization not found'), 'errors' => ['invite' => __('Invitation organization not found')]], 404);
            }
            // load all related organization's data
            $Organization->load(['members', 'slots']);
            // check for uwner/admin role
            Gate::forUser($User)->authorize('view', $Organization);
            Gate::forUser($Member)->authorize('view', $Organization);
            if (!$Organization->members->where('user_id', $Member->id)->whereNull('role')->first()) {
                return response()->json(['message' => __('Invite is not found'), 'errors' => ['invite' => __('Invite is not found')]], 404);
            }
            $Organization->members()->detach($Member);
            return response()->json(null, 204);
        }
        return null;
    }
}
