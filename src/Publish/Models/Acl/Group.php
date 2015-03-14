<?php

namespace App\Models\Acl;

use Illuminate\Database\Eloquent\Model;
use Signes\Acl\GroupInterface;

/**
 * Class Group
 *
 * @package    App\Models
 */
class Group extends Model implements GroupInterface
{

    /**
     * Application namespace
     *
     * @var string
     */
    protected $namespace = "App";

    /**
     * Mass fillable columns
     *
     * @var array
     */
    protected $fillable = ['name'];

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'acl_groups';

    /**
     * User group permissions
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function getPermissions()
    {
        return $this->belongsToMany(
            "{$this->namespace}\\Models\\Acl\\Permission",
            'acl_group_permissions',
            'group_id',
            'permission_id'
        )->withPivot('actions')->withTimestamps();
    }

    /**
     * Get group roles
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function getRoles()
    {
        return $this->belongsToMany(
            "{$this->namespace}\\Models\\Acl\\Role",
            'acl_group_roles',
            'group_id',
            'role_id'
        )->withTimestamps();
    }
}
