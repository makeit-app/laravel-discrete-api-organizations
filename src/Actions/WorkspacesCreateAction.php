<?php

namespace MakeIT\DiscreteApi\Organizations\Actions;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use MakeIT\DiscreteApi\Organizations\Contracts\WorkspacesCreateContract;
use MakeIT\Utils\Sorter;

class WorkspacesCreateAction extends WorkspacesCreateContract
{
    public function handle(User $User, array $input = []): ?JsonResponse
    {
        if (!app()->runningInConsole()) {
            if (!is_null($User->profile)) {
                $User->profile->load(['organization.workspaces', 'workspace']);
                Gate::forUser($User)->authorize('update', $User->profile->organization);
                $input['limit'] = $User->profile->organization->workspaces()->withTrashed()->count();
                Validator::make($input, [
                    'limit' => ['integer', 'required', 'max:'.(int) config('discreteapiorganizations.limit.organizations')],
                    'title' => ['required', 'string', 'max:100'],
                ], [
                    'limit' => __('You have reached your workspaces limit (including previously deleted workspaces). You can unlock the limit by contacting the support team with a request for final deletion of workspaces marked for deletion or purchase an additional workspace slot.'),
                ])->validateWithBag('createWorkspaceInformation');
                $Workspace = $User->profile->organization->workspaces()->create([
                    'title' => $input['title'],
                    'is_default' => false,
                    Sorter::FIELD => $User->profile->organization->workspaces()->withTrashed()->count() + 1,
                ]);
                $User->profile->forceFill(['workspace_id' => $Workspace->id])->save();
                $User->profile->load(['organization.workspaces', 'workspace']);
                $User->load(['organizations.workspaces']);

                return response()->json($Workspace->toArray());
            }

            return response()->json(null, 404);
        }
        return null;
    }
}
