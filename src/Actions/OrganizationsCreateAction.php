<?php

namespace MakeIT\DiscreteApi\Organizations\Actions;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use MakeIT\DiscreteApi\Organizations\Contracts\OrganizationsCreateContract;
use MakeIT\DiscreteApi\Organizations\Helpers\DiscreteApiOrganizationsHelper;
use MakeIT\DiscreteApi\Organizations\Models\Organization;
use MakeIT\Utils\Sorter;

class OrganizationsCreateAction extends OrganizationsCreateContract
{
    public function handle(User $User, array $input = []): ?JsonResponse
    {
        if (!app()->runningInConsole()) {
            if (!is_null($User->profile)) {
                $User->load(['organizations']);
                $User->profile->load(['organization.workspaces', 'workspace']);
                Gate::forUser($User)->allowIf($User->organizations()->withTrashed()->count() < DiscreteApiOrganizationsHelper::organizations_limit());
                Validator::make($input, [
                    'title' => ['required', 'string', 'max:100'],
                    'description' => ['string', 'max:4096'],
                ])->validateWithBag('createOrganizationInformation');
                $Organization = Organization::create([
                    'title' => $input['title'],
                    'description' => trim($input['description']),
                    'is_personal' => $User->organizations->count() == 0 ? true : false,
                    Sorter::FIELD => $User->organizations->count() + 1,
                ]);
                $User->organizations()->save($Organization, ['role' => 1]);
                $Organization->load(['workspaces']);
                $User->profile->forceFill([
                    'organization_id' => $Organization->id,
                    'workspace_id' => $Organization->workspaces->first()->id,
                ])->save();
                $User->profile->load(['organization.workspaces', 'workspace']);
                return response()->json($Organization->toArray());
            }
            return response()->json(null, 404);
        }
        return null;
    }
}
