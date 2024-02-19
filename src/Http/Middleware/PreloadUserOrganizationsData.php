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
            if (method_exists($request->user(), 'organizations') && method_exists($request->user()->profile, 'organization')) {
                $request->user()->load([
                    'profile' => function ($q) {
                        return $q->with([
                            'organization' => function ($q) {
                                return $q->with([
                                    'slots',
                                    'workspaces' => function ($q) {
                                        return $q->ordered();
                                        },
                                ]);
                            },
                            'workspace',
                        ]);
                    },
                    'organization_slots',
                    'organizations' => function ($q) {
                        return $q->ordered()->with([
                            'slots',
                            'workspaces' => function ($q) {
                                return $q->ordered();
                            },
                        ]);
                    },
                ]);
            }
        }

        return $next($request);
    }
}
