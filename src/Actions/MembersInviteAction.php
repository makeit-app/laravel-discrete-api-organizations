<?php

namespace MakeIT\DiscreteApi\Organizations\Actions;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use MakeIT\DiscreteApi\Organizations\Contracts\MembersInviteContract;
use MakeIT\DiscreteApi\Organizations\Events\InviteMemberToOrganizationEvent;

class MembersInviteAction extends MembersInviteContract
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
                $Slots = $Organization->slots;
                $input['limit'] = $Organization->members->count();
                // check for uwner/admin role
                Gate::forUser($User)->authorize('update', $Organization);
                // validate input
                Validator::make($input, [
                    'limit' => ['required', 'lt:'.$Slots->member_slots],
                    'email' => ['email', 'required'],
                    'role' => ['string', 'required'],
                ], [
                    'limit' => __('You have reached your organization\'s members limit. \nYou can unlock the limit by purchase an additional member slots for your organization.'),
                ])->validateWithBag('inviteNewMember');
                // extract member or 404
                $newMember = User::where('email', $input['email'])->firstOrFail();
                // check for duplicate
                $existingMember = $Organization->members()->where('user_id', $newMember->id)->count();
                if ($existingMember > 0) {
                    return response()->json([
                        'message' => __('Membership exists'),
                        'errors' => [
                            'role' => __('Membership exists'),
                        ],
                    ], 403);
                }
                // setup roles
                $roles = config('discreteapiorganizations.roles');
                unset($roles[1], $roles[9]); // unset owner(super) and invited roles from allowed roles list
                // check for input role
                if (!in_array($input['role'], $roles)) {
                    return response()->json([
                        'message' => __('Specified role is not found'),
                        'errors' => [
                            'role' => __('Specified role is not found'),
                        ],
                    ], 404);
                }
                // creating the membership
                $now = Carbon::now();
                $save = [
                    'invited_by' => $User->id,
                    'invited_at' => $now,
                    'invite_role' => array_search($input['role'], $roles),
                ];
                $newMember->organizations()->save($Organization, $save);
                $save['role'] = __(config('discreteapiorganizations')['role_titles'][$save['invite_role']]);
                InviteMemberToOrganizationEvent::dispatch($User, $newMember, $Organization, $save);
            }
            return response()->json(null, 204);
        } else {
            return response()->json(null, 404);
        }
    }
}
