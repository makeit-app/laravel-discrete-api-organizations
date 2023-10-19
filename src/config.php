<?php

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
    'policiesToRegister' => [
    ],
    /**
     * Observers. You are free to specify any full
     * qualifyed namespace to model and policy files
     */
    'observersToRegister' => [
    ],
    /**
     * Namespaces for class generator
     */
    'namespaces' => [
        'app' => '\\App\\', // `app/` directory
        'package' => '\\MakeIT\\DiscreteApi\\Organizations\\', // `package/src/` directory
    ],
];
