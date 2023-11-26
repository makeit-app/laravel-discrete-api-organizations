<?php

namespace MakeIT\DiscreteApi\Organizations\Contracts;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use MakeIT\DiscreteApi\Organizations\Models\Organization;

abstract class OrganizationsSwitchContract
{
    abstract public function handle(User $User, Organization $Organization): ?JsonResponse;
}
