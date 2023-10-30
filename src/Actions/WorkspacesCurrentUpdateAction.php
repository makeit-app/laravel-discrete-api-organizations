<?php

namespace MakeIT\DiscreteApi\Organizations\Actions;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use MakeIT\DiscreteApi\Organizations\Contracts\WorkspacesCurrentUpdateContract;

class WorkspacesCurrentUpdateAction extends WorkspacesCurrentUpdateContract
{
    public function handle(User $User, array $input = []): ?JsonResponse
    {
        if (!app()->runningInConsole()) {
            if (!is_null($User->profile)) {
                $User->profile->load(['organization.workspaces', 'workspace']);
                Gate::forUser($User)->authorize('update', $User->profile->workspace);
                Validator::make($input, [
                    'title' => ['required', 'string', 'max:100'],
                ])->validateWithBag('updateWorkspaceInformation');
                $User->profile->workspace->forceFill([
                    'title' => $input['title'],
                ])->save();
                $User->profile->load(['organization.workspaces', 'workspace']);
                return response()->json(null, 204);
            }
            return response()->json(null, 404);
        }
        return null;
    }
}
