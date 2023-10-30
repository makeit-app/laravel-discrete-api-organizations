<?php

namespace MakeIT\DiscreteApi\Organizations\Http\Controllers;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use MakeIT\DiscreteApi\Organizations\Contracts\WorkspacesCurrentUpdateContract;

class WorkspaceCurrentUpdateController extends BaseDiscreteApiOrganizationsController
{
    /**
     * @throws Exception
     */
    public function __invoke(Request $request): ?JsonResponse
    {
        return app(WorkspacesCurrentUpdateContract::class)->handle($request->user(), $request->only(['title', 'description']));
    }
}
