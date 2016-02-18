<?php

namespace Signes\Acl\Contract;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * Interface HavingPermissionsInterface
 *
 * @package Signes\Acl\Contract
 * @property \Illuminate\Database\Eloquent\Collection getPermissions
 */
interface HavingPermissionsInterface
{
    /**
     * Get object permissions
     *
     * @return BelongsToMany
     */
    public function getPermissions();
}
