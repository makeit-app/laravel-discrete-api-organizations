<?php

namespace MakeIT\DiscreteApi\Organizations\Actions;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use MakeIT\DiscreteApi\Organizations\Contracts\OrganizationsCurrentGetContract;

class OrganizationsListAction extends OrganizationsCurrentGetContract
{
    public function handle(User $User): JsonResponse
    {
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
}