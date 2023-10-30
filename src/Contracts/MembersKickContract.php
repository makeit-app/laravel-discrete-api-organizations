<?php

namespace MakeIT\DiscreteApi\Organizations\Contracts;

use App\Models\User;
use Illuminate\Http\JsonResponse;

abstract class MembersKickContract
{
    abstract public function handle(User $User, User $Member): ?JsonResponse;
}
