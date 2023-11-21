<?php

namespace MakeIT\DiscreteApi\Organizations\Actions;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use MakeIT\DiscreteApi\Organizations\Contracts\OrganizationsListContract;

class OrganizationsCurrentGetAction extends OrganizationsListContract
{
    public function handle(User $User): JsonResponse
    {
        if (!is_null($User->profile)) {
            return response()->json($User->organizations->toArray());
        } else {
            return response()->json(['errors' => __('User account without profiloe')], 404);
        }
    }
}