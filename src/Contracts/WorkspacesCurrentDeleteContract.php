<?php

namespace MakeIT\DiscreteApi\Organizations\Contracts;

use App\Models\User;
use Illuminate\Http\JsonResponse;

abstract class WorkspacesCurrentDeleteContract
{
    abstract public function handle(User $User): ?JsonResponse;
}
