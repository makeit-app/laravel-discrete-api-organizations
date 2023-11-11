<?php

namespace MakeIT\DiscreteApi\Organizations\Actions;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use MakeIT\DiscreteApi\Organizations\Contracts\OrganizationsCurrentUpdateContract;

class OrganizationsCurrentUpdateAction extends OrganizationsCurrentUpdateContract
{
    public function handle(User $User, array $input = []): JsonResponse
    {
    }
}