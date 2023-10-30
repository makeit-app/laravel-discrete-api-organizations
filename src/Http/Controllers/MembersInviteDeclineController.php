<?php

namespace MakeIT\DiscreteApi\Organizations\Http\Controllers;

use App\Models\User;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use MakeIT\DiscreteApi\Organizations\Contracts\MembersInviteDeclineContract;
use MakeIT\DiscreteApi\Organizations\Models\Organization;

class MembersInviteDeclineController extends BaseDiscreteApiOrganizationsController
{
    /**
     * @throws Exception
     */
    public function __invoke(Request $request, User $user, Organization $organization): ?JsonResponse
    {
        return app(MembersInviteDeclineContract::class)->handle($request->user(), $user, $organization);
    }
}
