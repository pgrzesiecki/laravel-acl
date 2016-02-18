<?php

namespace Signes\Acl;

use Illuminate\Support\Facades\Facade;

/**
 * Class AclFacade
 *
 * @package Signes\Acl
 */
class AclFacade extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'acl';
    }
}
