<?php

namespace MakeIT\DiscreteApi\Organizations\Actions;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use MakeIT\DiscreteApi\Organizations\Contracts\OrganizationsCurrentGetContract;

class OrganizationsCurrentGetAction extends OrganizationsCurrentGetContract
{
    public function handle(User $User): JsonResponse
    {
        $Profile = $User->profile;
        if (!is_null($Profile)) {
            dd($User->toArray());
            // загрузить через мидлварю оргу и пространство
            // выдать связку org->wspace
        } else {
            return response()->json(['errors' => __('User account without profiloe')], 404);
        }
        dd($User->toArray());
    }
}