<?php

use MakeIT\DiscreteApi\Organizations\Helpers\DiscreteApiOrganizationsHelper;

return [
    /**
     * What to use as route namespace
     * (where to look for controllers)
     * "package" -> look for package controllers to
     *      \MakeIT\DiscreteApi\Organizations\Http\Controllers
     * "app" -> look for application controllers placement
     *      \App\Http\Controllers\DiscreteApi\Organizations
     */
    'route_namespace' => 'package', // or "app"
    /**
     * Policies. You are free to specify any full
     * qualifyed namespace to model and policy files
     */
    'policiesToRegister' => [],
    /**
     * Observers. You are free to specify any full
     * qualifyed namespace to model and policy files
     */
    'observersToRegister' => [],
    /**
     * Namespaces for class generator
     */
    'namespaces' => [
        'app' => '\\App\\', // `app/` directory
        'package' => '\\MakeIT\\DiscreteApi\\Organizations\\', // `package/src/` directory
    ],
    /**
     * Limits for monetization
     */
    'limit' => [
        'organizations' => DiscreteApiOrganizationsHelper::organizatios_limit(),
        'workspaces' => DiscreteApiOrganizationsHelper::workspaces_limit()
    ],
    /**
     * Roles for identifying abilities
     *
     * 1 = OWNER (ability to remove organization and workspace)
     * 2 = Admin (full rights but not able to remove organization ro workspace // user and content manager)
     * 3 = user (can create/remove own content)
     * 4 = readonly user
     */
    'roles' => [
        1 => 'super',
        2 => 'admin',
        3 => 'user',
        4 => 'ro',
    ]
];
