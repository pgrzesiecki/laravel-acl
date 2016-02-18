<?php

namespace Signes\Acl\Contract;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * Interface HavingRolesInterface
 *
 * @package Signes\Acl\Contract
 * @property \Illuminate\Database\Eloquent\Collection getRoles
 */
interface HavingRolesInterface
{
    /**
     * Get user roles
     *
     * @return BelongsToMany
     */
    public function getRoles();
}
