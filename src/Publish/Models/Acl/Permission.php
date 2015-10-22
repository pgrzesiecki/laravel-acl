<?php

namespace App\Models\Acl;

use Illuminate\Database\Eloquent\Model;
use Signes\Acl\PermissionInterface;

/**
 * Class Permission
 *
 * @package App\Models\Acl
 */
class Permission extends Model implements PermissionInterface
{

    /**
     * Mass fillable columns
     *
     * @var array
     */
    protected $fillable = ['area', 'permission', 'actions', 'description'];

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'acl_permissions';

    /**
     * Set Permission are
     *
     * @param string $area , area name
     * @return $this
     */
    public function setArea($area)
    {
        $this->area = (string) $area;
        return $this;
    }

    /**
     * Return area name
     *
     * @return string
     */
    public function getArea()
    {
        return (string) $this->area;
    }

    /**
     * Set Permission permission
     *
     * @param string $permission , permission name
     * @return $this
     */
    public function setPermission($permission)
    {
        $this->permission = (string) $permission;
        return $this;
    }

    /**
     * Return permission name
     *
     * @return string
     */
    public function getPermission()
    {
        return (string) $this->permission;
    }


    /**
     * Set Permission actions
     *
     * @param array $actions , permission actions
     * @return $this
     */
    public function setActions(array $actions = [])
    {
        $this->actions = serialize($actions);
        return $this;
    }

    /**
     * Return actions array
     *
     * @return array
     */
    public function getActions()
    {
        return isset($this->actions) ? unserialize($this->actions) : [];
    }

    /**
     * Set Permission description
     *
     * @param string $description , permission description
     * @return $this
     */
    public function setDescription($description)
    {
        $this->description = (string) $description;
        return $this;
    }

    /**
     * Return description
     *
     * @return string
     */
    public function getDescription()
    {
        return (string) $this->description;
    }
}
