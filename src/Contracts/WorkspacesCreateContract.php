<?php

namespace MakeIT\DiscreteApi\Organizations\Contracts;

use App\Models\User;
use Illuminate\Http\JsonResponse;

abstract class WorkspacesCreateContract
{
    abstract public function handle(User $User, array $input = []): ?JsonResponse;
}
