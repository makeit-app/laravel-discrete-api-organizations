<?php

namespace MakeIT\DiscreteApi\Organizations\Contracts;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use MakeIT\DiscreteApi\Organizations\Models\Organization;

abstract class MembersInviteDeclineContract
{
    abstract public function handle(User $User, User $Member, Organization $Organization): ?JsonResponse;
}
