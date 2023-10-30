<?php

namespace MakeIT\DiscreteApi\Organizations\Http\Controllers;

use App\Models\User;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use MakeIT\DiscreteApi\Organizations\Contracts\MembersKickContract;

class MembersKickController extends BaseDiscreteApiOrganizationsController
{
    /**
     * @throws Exception
     */
    public function __invoke(Request $request, User $user): ?JsonResponse
    {
        return app(MembersKickContract::class)->handle($request->user(), $user);
    }
}
