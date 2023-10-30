<?php

namespace MakeIT\DiscreteApi\Organizations\Http\Controllers;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use MakeIT\DiscreteApi\Organizations\Contracts\MembersUpdateSettingsContract;

class MembersUpdateSettingsController extends BaseDiscreteApiOrganizationsController
{
    /**
     * @throws Exception
     */
    public function __invoke(Request $request): ?JsonResponse
    {
        return app(MembersUpdateSettingsContract::class)->handle($request->user(), $request->only(['user_id', 'role']));
    }
}
