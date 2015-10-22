<?php

namespace Signes\Acl;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Signes\Acl\Exception\UnknownRoleFilter;

/**
 * Interface RoleInterface
 *
 * @package Signes\Acl
 */
interface RoleInterface
{
    /**
     * Get user role permissions
     *
     * @return BelongsToMany
     */
    public function getPermissions();

    /**
     * Set special filter.
     *
     * @param string $filter      filter to set, available values:
     *                            A - allow access to everything
     *                            D - deny access to everything
     *                            R - revoke access to resource
     * @throws UnknownRoleFilter
     * @return $this
     */
    public function setFilter($filter);

    /**
     * Return special filter
     *
     * @return string filter, available options:
     *                A - allow access to everything
     *                D - deny access to everything
     *                R - revoke access to resource
     */
    public function getFilter();

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
