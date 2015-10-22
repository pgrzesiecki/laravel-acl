<?php

namespace Signes\Acl;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * Interface GroupInterface
 *
 * @package Signes\Acl
 */
interface GroupInterface
{

    /**
     * User group permissions
     *
     * @return BelongsToMany
     */
    public function getPermissions();

    /**
     * Get group roles
     *
     * @return BelongsToMany
     */
    public function getRoles();

    /**
     * Return all users belongs to group
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
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
