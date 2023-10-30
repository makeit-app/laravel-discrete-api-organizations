<?php

namespace MakeIT\DiscreteApi\Organizations\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class OrganizationsListController extends BaseDiscreteApiOrganizationsController
{
    public function __invoke(Request $request): JsonResponse
    {
    }
}
