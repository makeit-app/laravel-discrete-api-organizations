<?php

namespace MakeIT\DiscreteApi\Organizations\Actions;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use MakeIT\DiscreteApi\Organizations\Contracts\WorkspacesCreateContract;
use MakeIT\DiscreteApi\Organizations\Helpers\DiscreteApiOrganizationsHelper;
use MakeIT\Utils\Sorter;

class WorkspacesCreateAction extends WorkspacesCreateContract
{
    public function handle(User $User, array $input = []): ?JsonResponse
    {
        if (!app()->runningInConsole()) {
            if (!is_null($User->profile)) {
                $User->profile->load(['organization.workspaces', 'workspace']);
                Gate::forUser($User)->authorize('update', $User->profile->organization);
                Gate::forUser($User)->allowIf($User->profile->organization->workspaces()->withTrashed()->count() < DiscreteApiOrganizationsHelper::workspaces_limit());
                Validator::make($input, [
                    'title' => ['required', 'string', 'max:100'],
                    'description' => ['string', 'max:4096'],
                ])->validateWithBag('createWorkspaceInformation');
                $Workspace = $User->profile->organization->workspaces()->create([
                    'title' => $input['title'],
                    'description' => $input['description'],
                    'is_default' => $User->profile->organization->workspaces() == 0 ? true : false,
                    Sorter::FIELD => $User->profile->organization->workspaces->count() + 1,
                ]);
                $User->profile->workspace->forceFill([
                    'workspace_id' => $Workspace->id,
                ])->save();
                $User->profile->load(['organization.workspaces', 'workspace']);
                return response()->json($Workspace->toArray());
            }
            return response()->json(null, 404);
        }
        return null;
    }
}
