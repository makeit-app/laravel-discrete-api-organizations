<?php

namespace MakeIT\DiscreteApi\Organizations\Actions;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use MakeIT\DiscreteApi\Organizations\Contracts\OrganizationsCreateContract;
use MakeIT\DiscreteApi\Organizations\Models\Organization;
use MakeIT\Utils\Sorter;

class OrganizationsCreateAction extends OrganizationsCreateContract
{
    public function handle(User $User, array $input = []): ?JsonResponse
    {
        $Organization = null;
        if (!is_null($User->profile)) {
            $User->load(['organizations']);
            $User->profile->load(['organization.workspaces', 'workspace']);
            $input['limit'] = $User->organizations()->withTrashed()->count();
            $Slots = $User->organization_slots;
            if (!is_null($Slots)) {
                $limit = $User->organization_slots->slots;
            } else {
                $limit = config('discreteapiorganizations.limit.organizations');
            }
            unset($Slots);
            Validator::make($input, [
                'limit' => ['integer', 'required', 'lt:'.(int) $limit],
                'title' => ['required', 'string', 'max:100'],
                'description' => ['string', 'max:4096'],
            ], [
                'limit' => __('You have reached your organization limit (including previously deleted organizations). \nYou can unlock the limit by: \n(a) contacting the support team with a request for final deletion of organizations marked for deletion; \n(b) purchase an additional organization slot; \n(c) wait for 30 days to automated deletion..'),
            ])->validateWithBag('createOrganizationInformation');
            $Organization = Organization::create([
                'title' => $input['title'],
                'description' => trim($input['description']),
                'is_personal' => $User->organizations->count() == 0,
                Sorter::FIELD => $User->organizations->count() + 1,
            ]);
            $User->organization_slots()->create([
                'organization_id' => $Organization->id,
                'slots' => $limit,
            ]);
            $now = Carbon::now();
            $User->organizations()->save($Organization, [
                'role' => 1,
                'invited_by' => $User->id,
                'invited_at' => $now,
                'invite_role' => 1,
                'invite_confirmed_at' => $now,
            ]);
            $Organization->load(['workspaces']);
            $User->profile->forceFill([
                'organization_id' => $Organization->id,
                'workspace_id' => $Organization->workspaces->first()->id,
            ])->save();
            $User->load([
                'profile' => function ($q) {
                    return $q->with([
                        'organization' => function ($q) {
                            return $q->with([
                                'slots',
                                'workspaces' => function ($q) {
                                    return $q->ordered();
                                },
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
                    ]);
                },
            ]);
        }
        if (!app()->runningInConsole()) {
            if ($Organization instanceof Organization) {
                return response()->json($Organization->toArray());
            } else {
                return response()->json(null, 404);
            }
        } else {
            return null;
        }
    }
}
