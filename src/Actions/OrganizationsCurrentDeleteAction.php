<?php

namespace MakeIT\DiscreteApi\Organizations\Actions;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use MakeIT\DiscreteApi\Organizations\Contracts\OrganizationsCurrentDeleteContract;

class OrganizationsCurrentDeleteAction extends OrganizationsCurrentDeleteContract
{
    public function handle(User $User): JsonResponse
    {
    }
}