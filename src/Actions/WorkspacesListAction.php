<?php

namespace MakeIT\DiscreteApi\Organizations\Actions;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use MakeIT\DiscreteApi\Organizations\Contracts\WorkspacesListContract;

class WorkspacesListAction extends WorkspacesListContract
{
    public function handle(User $User): ?JsonResponse
    {
        if (!app()->runningInConsole()) {
            $User->profile->load([
                'organization' => function ($q) {
                    return $q->ordered()->with([
                        'workspaces' => function ($q) {
                            return $q->ordered();
                        }
                    ]);
                },
                'workspace'
            ]);
            if (!is_null($User->profile->organization) && $User->profile->organization->workspaces->count() > 0) {
                $Workspace = $User->profile->workspace;
                $User->profile->organization->workspaces->each(function (&$item) use ($Workspace) {
                    $item->is_current = $item->id === $Workspace->id;
                });
                return response()->json($User->profile->organization->workspaces->toArray());
            } else {
                return response()->json([]);
            }
        }
        return null;
    }
}
