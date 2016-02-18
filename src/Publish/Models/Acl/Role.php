<?php

namespace App\Models\Acl;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Signes\Acl\Exception\UnknownRoleFilterException;
use Signes\Acl\Contract\RoleInterface;

/**
 * Class Role
 *
 * @package App\Models\Acl
 */
class Role extends Model implements RoleInterface
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
    protected $fillable = ['name', 'filter'];

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'acl_roles';

    /**
     * Available values:
     *  A - allow access to everything
     *  D - deny access to everything
     *  R - revoke access to resource
     *
     * @var array
     */
    protected $filters = ['A', 'D', 'R'];

    /**
     * User role permissions
     *
     * @return BelongsToMany
     */
    public function getPermissions()
    {
        return $this->belongsToMany(
            "{$this->namespace}\\Models\\Acl\\Permission",
            'acl_role_permissions',
            'role_id',
            'permission_id'
        )->withPivot('actions')->withTimestamps();
    }

    /**
     * Set special filter.
     *
     * @param string $filter      filter to set, available values:
     *                            A - allow access to everything
     *                            D - deny access to everything
     *                            R - revoke access to resource
     * @return $this
     * @throws UnknownRoleFilterException
     */
    public function setFilter($filter)
    {
        $filter = (string) $filter;
        if (!in_array($filter, $this->filters)) {
            throw new UnknownRoleFilterException("Unknown role filter: '{$filter}'");
        }

        $this->filter = $filter;

        return $this;
    }

    /**
     * Return special filter
     *
     * @return string filter, available options:
     *                A - allow access to everything
     *                D - deny access to everything
     *                R - revoke access to resource
     */
    public function getFilter()
    {
        return (string) $this->filter;
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
