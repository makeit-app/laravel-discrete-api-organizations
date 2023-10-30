<?php

namespace MakeIT\DiscreteApi\Organizations\Http\Controllers;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use MakeIT\DiscreteApi\Organizations\Contracts\OrganizationsCurrentUpdateContract;

class OrganizationCurrentUpdateController extends BaseDiscreteApiOrganizationsController
{
    /**
     * @throws Exception
     */
    public function __invoke(Request $request): ?JsonResponse
    {
        return app(OrganizationsCurrentUpdateContract::class)->handle($request->user(), $request->only(['title', 'description']));
    }
}
