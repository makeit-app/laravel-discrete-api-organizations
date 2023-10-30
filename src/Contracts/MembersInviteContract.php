<?php

namespace MakeIT\DiscreteApi\Organizations\Contracts;

use App\Models\User;
use Illuminate\Http\JsonResponse;

abstract class MembersInviteContract
{
    abstract public function handle(User $User, array $input = []): ?JsonResponse;
}
