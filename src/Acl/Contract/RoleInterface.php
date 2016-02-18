<?php

namespace Signes\Acl\Contract;

use Signes\Acl\Exception\UnknownRoleFilterException;

/**
 * Interface RoleInterface
 *
 * @package Signes\Acl\Contract
 */
interface RoleInterface extends HavingPermissionsInterface
{
    /**
     * Set special filter.
     *
     * @param string $filter      filter to set, available values:
     *                            A - allow access to everything
     *                            D - deny access to everything
     *                            R - revoke access to resource
     * @throws UnknownRoleFilterException
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
