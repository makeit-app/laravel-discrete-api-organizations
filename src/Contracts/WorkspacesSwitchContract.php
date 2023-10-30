<?php

namespace MakeIT\DiscreteApi\Organizations\Contracts;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use MakeIT\DiscreteApi\Organizations\Models\Workspace;

abstract class WorkspacesSwitchContract
{
    abstract public function handle(User $User, Workspace $Workspace): ?JsonResponse
    ;
}
