<?php

namespace Signes\Acl;

/**
 * Interface PermissionInterface
 *
 * @package Signes\Acl
 */
interface PermissionInterface
{
    /**
     * Set Permission are
     *
     * @param string $area , area name
     * @return $this
     */
    public function setArea($area);

    /**
     * Return area name
     *
     * @return string
     */
    public function getArea();

    /**
     * Set Permission permission
     *
     * @param string $permission , permission name
     * @return $this
     */
    public function setPermission($permission);

    /**
     * Return permission name
     *
     * @return string
     */
    public function getPermission();

    /**
     * Set Permission actions
     *
     * @param array $actions , permission actions
     * @return $this
     */
    public function setActions(array $actions = []);

    /**
     * Return actions array
     *
     * @return array
     */
    public function getActions();

    /**
     * Set Permission description
     *
     * @param string $description , permission description
     * @return $this
     */
    public function setDescription($description);

    /**
     * Return description
     *
     * @return string
     */
    public function getDescription();
}
