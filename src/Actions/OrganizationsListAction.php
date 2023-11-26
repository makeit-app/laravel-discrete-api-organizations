<?php

namespace MakeIT\DiscreteApi\Organizations\Actions;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use MakeIT\DiscreteApi\Organizations\Contracts\OrganizationsListContract;

class OrganizationsListAction extends OrganizationsListContract
{
    public function handle(User $User): ?JsonResponse
    {
        if (!app()->runningInConsole()) {
            $User->load([
                'organizations' => function ($q) {
                    return $q->ordered()->with([
                        'workspaces' => function ($q) {
                            return $q->ordered();
                        }
                    ]);
                }
            ]);
            return response()->json($User->organizations->toArray());
        }
        return null;
    }
}
