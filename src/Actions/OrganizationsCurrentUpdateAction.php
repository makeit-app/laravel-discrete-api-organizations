<?php

namespace MakeIT\DiscreteApi\Organizations\Actions;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use MakeIT\DiscreteApi\Organizations\Contracts\OrganizationsCurrentUpdateContract;

class OrganizationsCurrentUpdateAction extends OrganizationsCurrentUpdateContract
{
    public function handle(User $User, array $input = []): ?JsonResponse
    {
        if (!app()->runningInConsole()) {
            if (!is_null($User->profile)) {
                $User->profile->load(['organization.workspaces', 'workspace']);
                Gate::forUser($User)->authorize('update', $User->profile->organization);
                Validator::make($input, [
                    'title' => ['required', 'string', 'max:100'],
                    'description' => ['string', 'max:4096'],
                ])->validateWithBag('updateOrganizationInformation');
                $User->profile->organization->forceFill([
                    'title' => $input['title'],
                    'description' => $input['description'],
                ])->save();
                $User->profile->load(['organization.workspaces', 'workspace']);
                return response()->json(null, 204);
            }
            return response()->json(null, 404);
        }
        return null;
    }
}
