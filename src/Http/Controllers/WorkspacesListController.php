<?php

namespace MakeIT\DiscreteApi\Organizations\Http\Controllers;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use MakeIT\DiscreteApi\Organizations\Contracts\WorkspacesListContract;

class WorkspacesListController extends BaseDiscreteApiOrganizationsController
{
    /**
     * @throws Exception
     */
    public function __invoke(Request $request): ?JsonResponse
    {
        return app(WorkspacesListContract::class)->handle($request->user());
    }
}
