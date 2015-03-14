<?php

namespace Signes\Acl\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * Class Acl
 *
 * @package    Signes\Acl
 * @subpackage Signes\Acl\Facades
 */
class Acl extends Facade
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
