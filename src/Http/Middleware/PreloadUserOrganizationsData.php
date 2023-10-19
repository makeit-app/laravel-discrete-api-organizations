<?php

namespace MakeIT\DiscreteApi\Profile\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PreloadUserProfileData
{
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check()) {
            $request->user()->load([
                'profile.organization',
                'profile.organization.workspace',
                'profile.organizations',
                'profile.organizations.workspaces',
            ]);
        }

        return $next($request);
    }
}
