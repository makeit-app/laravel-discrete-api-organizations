<?php

namespace MakeIT\DiscreteApi\Organizations\Http\Controllers;

use App\Models\User;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use MakeIT\DiscreteApi\Organizations\Contracts\MembersInviteAcceptContract;
use MakeIT\DiscreteApi\Organizations\Models\Organization;

class MembersInviteAcceptController extends BaseDiscreteApiOrganizationsController
{
    /**
     * @throws Exception
     */
    public function __invoke(Request $request, User $user, Organization $organization): ?JsonResponse
    {
        //return ! $request->hasValidRelativeSignature() ? response()->json(['user' => $user->toArray(), 'request' => $request->all(), 'hasValidSignature' => $request->hasValidRelativeSignature()], 401) : response()->json(null, 204);
        return app(MembersInviteAcceptContract::class)->handle($request->user(), $user, $organization);
    }
}
