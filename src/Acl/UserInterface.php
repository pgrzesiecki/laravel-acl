<?php

namespace Signes\Acl;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * Interface UserInterface
 *
 * @package Signes\Acl
 */
interface UserInterface
{
    /**
     * Get user personal permissions
     *
     * @return BelongsToMany
     */
    public function getPermissions();

    /**
     * Get user roles
     *
     * @return BelongsToMany
     */
    public function getRoles();

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
