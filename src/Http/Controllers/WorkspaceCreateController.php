<?php

namespace MakeIT\DiscreteApi\Organizations\Http\Controllers;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use MakeIT\DiscreteApi\Organizations\Contracts\WorkspaceCreateContract;

class WorkspaceCreateController extends BaseDiscreteApiOrganizationsController
{
    /**
     * @throws Exception
     */
    public function __invoke(Request $request): ?JsonResponse
    {
        return app(WorkspaceCreateContract::class)->handle($request->user(), $request->only(['title','description']));
    }
}
