<?php

namespace MakeIT\DiscreteApi\Organizations\Http\Controllers;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OrganizationsListController extends BaseDiscreteApiOrganizationsController
{
    /**
     * @throws Exception
     */
    public function __invoke(Request $request): JsonResponse
    {
        return app()->handle($request->user());
    }
}
