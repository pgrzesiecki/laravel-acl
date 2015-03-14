<?php

namespace Signes\Acl;

/**
 * Interface GroupInterface
 *
 * @package Signes\Acl
 */
interface GroupInterface
{

    public function getPermissions();

    public function getRoles();
}
