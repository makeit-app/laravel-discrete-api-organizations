<?php

namespace MakeIT\DiscreteApi\Organizations\Actions;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use MakeIT\DiscreteApi\Organizations\Contracts\MembersInviteContract;

class MembersUpdateSettingsAction extends MembersInviteContract
{
    public function handle(User $User, array $input = []): ?JsonResponse
    {
        if (!app()->runningInConsole()) {
            // preload user data
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
            // check for organization existance
            if (!is_null($Organization)) {
                // check for owner/admin role
                Gate::forUser($User)->authorize('update', $Organization);
                // setup roles
                $roles = config('discreteapiorganizations.roles');
                unset($roles[1], $roles[9]); // unset owner(super) and invited roles from allowed roles list
                // validate input
                Validator::make($input, [
                    'user_id' => ['uuid', 'required', 'exists:users,id'],
                    'role' => ['string', 'required', 'in:'.implode(',', $roles)],
                ], [
                    'user_id' => __('User (ID: :uuid) is not found', ['uuid' => $input['user_id']]),
                    'role' => __('Invalid Role'),
                ])->validateWithBag('updateMember');
                // extract member or 404
                $Member = User::with([
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
                ])->find($input['user_id']);
                // check for member is in organization
                Gate::forUser($Member)->authorize('view', $Organization);
                // check for input role
                if (!in_array($input['role'], $roles)) {
                    return response()->json([
                        'message' => __('Specified role is not found'),
                        'errors' => [
                            'role' => __('Specified role is not found'),
                        ],
                    ], 404);
                }
                // get the membership record for futher modifications or 404 (404 not real, but close this hole)
                if ($Organization->members()->where('user_id', $input['user_id'])->where('role', '>', 0)->count()) {
                    // updating the membership
                    $now = Carbon::now();
                    $Organization->members()->updateExistingPivot($Member, [
                        'role' => array_search($input['role'], $roles),
                        'updated_by' => $User->id,
                        'updated_at' => $now,
                    ]);
                } else {
                    return response()->json(['message' => __('Membership is not found'), 'errors' => ['membership' => __('Membership is not found')]], 404);
                }
            }

            return response()->json(null, 204);
        }

        return null;
    }
}
