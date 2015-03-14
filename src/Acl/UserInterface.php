<?php

namespace Signes\Acl;

/**
 * Interface UserInterface
 *
 * @package Signes\Acl
 */
interface UserInterface
{
    public function getPermissions();

    public function getRoles();

    public function getGroup();
}
