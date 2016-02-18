<?php

namespace Signes\Acl\Contract;

use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * Interface UserInterface
 *
 * @package Signes\Acl\Contract
 * @property \Signes\Acl\Contract\GroupInterface getGroup
 */
interface UserInterface extends HavingPermissionsInterface, HavingRolesInterface
{
    /**
     * Get user group
     *
     * @return HasOne
     */
    public function getGroup();

    /**
     * Set user group
     *
     * @param GroupInterface $group
     * @return $this
     */
    public function setGroup(GroupInterface $group);
}
