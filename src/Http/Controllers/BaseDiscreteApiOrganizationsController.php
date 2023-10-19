<?php

namespace MakeIT\DiscreteApi\Organizations\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class BaseDiscreteApiOrganizationsController extends BaseController
{
    use AuthorizesRequests;
    use ValidatesRequests;
}
