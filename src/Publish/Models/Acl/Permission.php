<?php

namespace App\Models\Acl;

use Illuminate\Database\Eloquent\Model;
use Signes\Acl\PermissionInterface;

/**
 * Class Permission
 *
 * @package Signes\Acl\Model
 */
class Permission extends Model implements PermissionInterface
{

    /**
     * Mass fillable columns
     *
     * @var array
     */
    protected $fillable = array('area', 'permission', 'actions', 'description');

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'acl_permissions';
}
