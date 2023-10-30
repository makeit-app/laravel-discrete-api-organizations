<?php

namespace MakeIT\DiscreteApi\Organizations\Contracts;

use App\Models\User;
use Illuminate\Http\JsonResponse;

abstract class OrganizationsListContract
{
    abstract public function handle(User $User): ?JsonResponse;
}
