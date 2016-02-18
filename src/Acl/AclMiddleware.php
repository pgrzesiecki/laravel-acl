<?php

namespace Signes\Acl;

use Closure;
use Signes\Acl\Exception\AclAccessException;

/**
 * Class AclMiddleware
 *
 * @package Signes\Acl
 */
class AclMiddleware
{
    /**
     * ACL Middleware
     *
     * @param $request
     * @param Closure $next
     * @param $access
     * @return mixed
     * @throws AclAccessException
     */
    public function handle($request, Closure $next, $access)
    {
        if (!app('acl')->isAllow($access)) {
            throw new AclAccessException("You do not have access to this resource.");
        }

        return $next($request);
    }
}
