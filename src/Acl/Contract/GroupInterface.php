<?php

namespace Signes\Acl\Contract;

use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Interface GroupInterface

 *
*@package Signes\Acl\Contract
 * @property \Illuminate\Database\Eloquent\Collection getUsers
 */
interface GroupInterface extends HavingPermissionsInterface, HavingRolesInterface
{
    /**
     * Return all users belongs to group
     *
     * @return HasMany
     */
    public function getUsers();

    /**
     * Set group name
     *
     * @param string $name
     * @return $this
     */
    public function setName($name);

    /**
     * Return group name
     *
     * @return string
     */
    public function getName();
}
