<?php

namespace MakeIT\DiscreteApi\Organizations\Http\Controllers;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use MakeIT\DiscreteApi\Organizations\Contracts\OrganizationsSwitchContract;
use MakeIT\DiscreteApi\Organizations\Models\Organization;

class OrganizationSwitchController extends BaseDiscreteApiOrganizationsController
{
    /**
     * @throws Exception
     */
    public function __invoke(Request $request, Organization $Organization): ?JsonResponse
    {
        return app(OrganizationsSwitchContract::class)->handle($request->user(), $Organization);
    }
}
