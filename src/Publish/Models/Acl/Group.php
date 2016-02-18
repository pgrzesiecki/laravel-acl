<?php

namespace App\Models\Acl;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Signes\Acl\Contract\GroupInterface;

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
     * @return BelongsToMany
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
     * @return BelongsToMany
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

    /**
     * Return all users belongs to group
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function getUsers()
    {
        return $this->hasMany("{$this->namespace}\\Models\\Acl\\User", "group_id", "id");
    }

    /**
     * Set group name
     *
     * @param string $name
     * @return $this
     */
    public function setName($name)
    {
        $this->name = (string) $name;
        return $this;
    }

    /**
     * Return group name
     *
     * @return string
     */
    public function getName()
    {
        return (string) $this->name;
    }
}
