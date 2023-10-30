<?php

namespace MakeIT\DiscreteApi\Organizations\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OrganizationsListController extends BaseDiscreteApiOrganizationsController
{
    public function __invoke(Request $request): JsonResponse
    {
    }
}
