<?php

namespace MakeIT\DiscreteApi\Organizations\Helpers;

class DiscreteApiOrganizationsHelper
{
    public static function organizatios_limit(): int
    {
        /**
         * TODO: Subscription
         * - read limits from subscription
         */
        switch(config('app.env')){
            case 'local':
                return 2;
            default:
            case 'production':
                return 1;
        }
    }
    public static function workspaces_limit(): int
    {
        /**
         * TODO: Subscription
         * - read limits from subscription
         */
        switch(config('app.env')){
            case 'local':
                return 3;
            default:
            case 'production':
                return 1;
        }
    }
}