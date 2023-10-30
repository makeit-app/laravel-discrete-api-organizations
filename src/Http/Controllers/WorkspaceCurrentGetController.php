<?php

namespace MakeIT\DiscreteApi\Organizations\Http\Controllers;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use MakeIT\DiscreteApi\Organizations\Contracts\WorkspacesCurrentGetContract;

class WorkspaceCurrentGetController extends BaseDiscreteApiOrganizationsController
{
    /**
     * @throws Exception
     */
    public function __invoke(Request $request): ?JsonResponse
    {
        return app(WorkspacesCurrentGetContract::class)->handle($request->user());
    }
}
