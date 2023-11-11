<?php

namespace MakeIT\DiscreteApi\Organizations\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PreloadUserOrganizationsData
{
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check()) {
            $request->user()->load([
                'profile.organization',
                'organizations.workspaces',
            ]);
        }

        return $next($request);
    }
}
