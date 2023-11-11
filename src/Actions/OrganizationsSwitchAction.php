<?php

namespace MakeIT\DiscreteApi\Organizations\Actions;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use MakeIT\DiscreteApi\Organizations\Contracts\OrganizationsSwitchContract;
use MakeIT\DiscreteApi\Organizations\Models\Organization;

class OrganizationsSwitchAction extends OrganizationsSwitchContract
{
    public function handle(User $User, Organization $Organization): JsonResponse
    {
        // check if user in organization
        // if yes - switch => update profile
        // if no - abort
    }
}