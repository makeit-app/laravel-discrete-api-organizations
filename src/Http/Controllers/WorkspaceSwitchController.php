<?php

namespace MakeIT\DiscreteApi\Organizations\Http\Controllers;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use MakeIT\DiscreteApi\Organizations\Contracts\WorkspacesSwitchContract;
use MakeIT\DiscreteApi\Organizations\Models\Workspace;

class WorkspaceSwitchController extends BaseDiscreteApiOrganizationsController
{
    /**
     * @throws Exception
     */
    public function __invoke(Request $request, Workspace $Workspace): ?JsonResponse
    {
        return app(WorkspacesSwitchContract::class)->handle($request->user(), $Workspace);
    }
}
