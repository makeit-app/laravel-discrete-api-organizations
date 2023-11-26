<?php

namespace MakeIT\DiscreteApi\Organizations\Contracts;

use App\Models\User;
use Illuminate\Http\JsonResponse;

abstract class WorkspacesListContract
{
    abstract public function handle(User $User): ?JsonResponse;
}
