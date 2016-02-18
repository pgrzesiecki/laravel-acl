<?php

namespace Signes\Acl;

use Mockery;
use Signes\Acl\Contract\AclRepository;
use Signes\Acl\Contract\GroupInterface;
use Signes\Acl\Contract\PermissionInterface;
use Signes\Acl\Contract\RoleInterface;
use Signes\Acl\Contract\UserInterface;

/**
 * Class AclTest
 * @coversDefaultClass \Signes\Acl\Acl
 *
 * @group signes_acl
 */
class AclTest extends TestCase
{
    //        public function testCallIsAllow()
    //        {
    //
    //            $resource = 'resource';
    //            $user = Mockery::mock(UserInterface::class);
    //
    //            $mockRepository = Mockery::mock(AclRepository::class);
    //
    //            $object = new Acl($mockRepository);
    //            $response = $object->isAllow($resource, $user);
    //            $this->assertTrue($response);
    //        }

    /**
     * Test proxy call `createPermission`
     * @covers ::createPermission
     */
    public function testCallCreatePermissions()
    {
        $area = 'area';
        $permission = 'permission';
        $action = [];
        $description = 'description';

        $mockRepository = Mockery::mock(AclRepository::class);
        $mockRepository->shouldReceive('createPermission')
            ->with($area, $permission, $action, $description)
            ->once()
            ->andReturn(true);

        $object = new Acl($mockRepository);
        $response = $object->createPermission($area, $permission, $action, $description);
        $this->assertTrue($response);
    }


    /**
     * Test proxy call `deletePermission`
     * @covers ::deletePermission
     */
    public function testCallDeletePermission()
    {
        $area = 'area';
        $permission = 123;
        $action = [];

        $mockRepository = Mockery::mock(AclRepository::class);
        $mockRepository->shouldReceive('deletePermission')
            ->with($area, (string) $permission, $action)
            ->once()
            ->andReturn(true);

        $object = new Acl($mockRepository);
        $response = $object->deletePermission($area, $permission, $action);
        $this->assertTrue($response);
    }

    /**
     * Test proxy call `grantUserPermission`
     * @covers ::grantUserPermission
     */
    public function testCallGrantUserPermission()
    {
        $permission = Mockery::mock(PermissionInterface::class);
        $user = Mockery::mock(UserInterface::class);
        $actions = [];
        $overwrite = false;

        $mockRepository = Mockery::mock(AclRepository::class);
        $mockRepository->shouldReceive('grantUserPermission')->with($permission, $user, $actions)->once()->andReturn(
                true
            );

        $object = new Acl($mockRepository);
        $response = $object->grantUserPermission($permission, $user, $actions, $overwrite);
        $this->assertTrue($response);
    }

    /**
     * Test proxy call `grantUserPermission`
     * @covers ::grantUserPermission
     */
    public function testCallGrantUserPermissionWithOverwrite()
    {
        $permission = Mockery::mock(PermissionInterface::class);
        $user = Mockery::mock(UserInterface::class);
        $actions = [];
        $overwrite = true;

        $mockRepository = Mockery::mock(AclRepository::class);
        $mockRepository->shouldReceive('grantUserPermission')->with($permission, $user, $actions)->once()->andReturn(
                true
            );
        $mockRepository->shouldReceive('revokeUserPermission')->with($permission, $user)->once()->andReturnNull();

        $object = new Acl($mockRepository);
        $response = $object->grantUserPermission($permission, $user, $actions, $overwrite);
        $this->assertTrue($response);
    }

    /**
     * Test proxy call `revokeUserPermission`
     * @covers ::revokeUserPermission
     */
    public function testCallRevokeUserPermission()
    {
        $permission = Mockery::mock(PermissionInterface::class);
        $user = Mockery::mock(UserInterface::class);

        $mockRepository = Mockery::mock(AclRepository::class);
        $mockRepository->shouldReceive('revokeUserPermission')->with($permission, $user)->once()->andReturn(true);

        $object = new Acl($mockRepository);
        $response = $object->revokeUserPermission($permission, $user);
        $this->assertTrue($response);
    }

    /**
     * Test proxy call `grantGroupPermission`
     * @covers ::grantGroupPermission
     */
    public function testCallGrantGroupPermission()
    {
        $permission = Mockery::mock(PermissionInterface::class);
        $group = Mockery::mock(GroupInterface::class);
        $actions = [];
        $overwrite = false;

        $mockRepository = Mockery::mock(AclRepository::class);
        $mockRepository->shouldReceive('grantGroupPermission')->with($permission, $group, $actions)->once()->andReturn(
                true
            );

        $object = new Acl($mockRepository);
        $response = $object->grantGroupPermission($permission, $group, $actions, $overwrite);
        $this->assertTrue($response);
    }

    /**
     * Test proxy call `grantGroupPermission`
     * @covers ::grantGroupPermission
     */
    public function testCallGrantGroupPermissionWithOverwrite()
    {
        $permission = Mockery::mock(PermissionInterface::class);
        $group = Mockery::mock(GroupInterface::class);
        $actions = [];
        $overwrite = true;

        $mockRepository = Mockery::mock(AclRepository::class);
        $mockRepository->shouldReceive('grantGroupPermission')->with($permission, $group, $actions)->once()->andReturn(
                true
            );
        $mockRepository->shouldReceive('revokeGroupPermission')->with($permission, $group)->once()->andReturnNull();

        $object = new Acl($mockRepository);
        $response = $object->grantGroupPermission($permission, $group, $actions, $overwrite);
        $this->assertTrue($response);
    }

    /**
     * Test proxy call `revokeGroupPermission`
     * @covers ::revokeGroupPermission
     */
    public function testCallRevokeGroupPermission()
    {
        $permission = Mockery::mock(PermissionInterface::class);
        $group = Mockery::mock(GroupInterface::class);

        $mockRepository = Mockery::mock(AclRepository::class);
        $mockRepository->shouldReceive('revokeGroupPermission')->with($permission, $group)->once()->andReturn(true);

        $object = new Acl($mockRepository);
        $response = $object->revokeGroupPermission($permission, $group);
        $this->assertTrue($response);
    }


    /**
     * Test proxy call `grantRolePermission`
     * @covers ::grantRolePermission
     */
    public function testCallGrantRolePermission()
    {
        $permission = Mockery::mock(PermissionInterface::class);
        $role = Mockery::mock(RoleInterface::class);
        $actions = [];
        $overwrite = false;

        $mockRepository = Mockery::mock(AclRepository::class);
        $mockRepository->shouldReceive('grantRolePermission')->with($permission, $role, $actions)->once()->andReturn(
                true
            );

        $object = new Acl($mockRepository);
        $response = $object->grantRolePermission($permission, $role, $actions, $overwrite);
        $this->assertTrue($response);
    }

    /**
     * Test proxy call `grantRolePermission`
     * @covers ::grantRolePermission
     */
    public function testCallGrantRolePermissionWithOverwrite()
    {
        $permission = Mockery::mock(PermissionInterface::class);
        $role = Mockery::mock(RoleInterface::class);
        $actions = [];
        $overwrite = true;

        $mockRepository = Mockery::mock(AclRepository::class);
        $mockRepository->shouldReceive('grantRolePermission')->with($permission, $role, $actions)->once()->andReturn(
                true
            );
        $mockRepository->shouldReceive('revokeRolePermission')->with($permission, $role)->once()->andReturnNull();

        $object = new Acl($mockRepository);
        $response = $object->grantRolePermission($permission, $role, $actions, $overwrite);
        $this->assertTrue($response);
    }

    /**
     * Test proxy call `revokeRolePermission`
     * @covers ::revokeRolePermission
     */
    public function testCallRevokeRolePermission()
    {
        $permission = Mockery::mock(PermissionInterface::class);
        $role = Mockery::mock(RoleInterface::class);

        $mockRepository = Mockery::mock(AclRepository::class);
        $mockRepository->shouldReceive('revokeRolePermission')->with($permission, $role)->once()->andReturn(true);

        $object = new Acl($mockRepository);
        $response = $object->revokeRolePermission($permission, $role);
        $this->assertTrue($response);
    }

    /**
     * Test proxy call `grantUserRole`
     * @covers ::grantUserRole
     */
    public function testCallGrantUserRole()
    {
        $role = Mockery::mock(RoleInterface::class);
        $user = Mockery::mock(UserInterface::class);
        $overwrite = false;

        $mockRepository = Mockery::mock(AclRepository::class);
        $mockRepository->shouldReceive('grantUserRole')->with($role, $user)->once()->andReturn(true);

        $object = new Acl($mockRepository);
        $response = $object->grantUserRole($role, $user, $overwrite);
        $this->assertTrue($response);
    }

    /**
     * Test proxy call `grantUserRole`
     * @covers ::grantUserRole
     */
    public function testCallGrantUserRoleWithOverwrite()
    {
        $role = Mockery::mock(RoleInterface::class);
        $user = Mockery::mock(UserInterface::class);
        $overwrite = true;

        $mockRepository = Mockery::mock(AclRepository::class);
        $mockRepository->shouldReceive('grantUserRole')->with($role, $user)->once()->andReturn(true);
        $mockRepository->shouldReceive('revokeUserRole')->with($role, $user)->once()->andReturnNull();

        $object = new Acl($mockRepository);
        $response = $object->grantUserRole($role, $user, $overwrite);
        $this->assertTrue($response);
    }

    /**
     * Test proxy call `revokeUserRole`
     * @covers ::revokeUserRole
     */
    public function testCallRevokeUserRole()
    {
        $role = Mockery::mock(RoleInterface::class);
        $user = Mockery::mock(UserInterface::class);

        $mockRepository = Mockery::mock(AclRepository::class);
        $mockRepository->shouldReceive('revokeUserRole')->with($role, $user)->once()->andReturn(true);

        $object = new Acl($mockRepository);
        $response = $object->revokeUserRole($role, $user);
        $this->assertTrue($response);
    }

    /**
     * Test proxy call `grantGroupRole`
     * @covers ::grantGroupRole
     */
    public function testCallGrantGroupRole()
    {
        $role = Mockery::mock(RoleInterface::class);
        $group = Mockery::mock(GroupInterface::class);
        $overwrite = false;

        $mockRepository = Mockery::mock(AclRepository::class);
        $mockRepository->shouldReceive('grantGroupRole')->with($role, $group)->once()->andReturn(true);

        $object = new Acl($mockRepository);
        $response = $object->grantGroupRole($role, $group, $overwrite);
        $this->assertTrue($response);
    }

    /**
     * Test proxy call `grantGroupRole`
     * @covers ::grantGroupRole
     */
    public function testCallGrantGroupRoleWithOverwrite()
    {
        $role = Mockery::mock(RoleInterface::class);
        $group = Mockery::mock(GroupInterface::class);
        $overwrite = true;

        $mockRepository = Mockery::mock(AclRepository::class);
        $mockRepository->shouldReceive('grantGroupRole')->with($role, $group)->once()->andReturn(true);
        $mockRepository->shouldReceive('revokeGroupRole')->with($role, $group)->once()->andReturnNull();

        $object = new Acl($mockRepository);
        $response = $object->grantGroupRole($role, $group, $overwrite);
        $this->assertTrue($response);
    }

    /**
     * Test proxy call `revokeGroupRole`
     * @covers ::revokeGroupRole
     */
    public function testCallRevokeGroupRole()
    {
        $role = Mockery::mock(RoleInterface::class);
        $group = Mockery::mock(GroupInterface::class);

        $mockRepository = Mockery::mock(AclRepository::class);
        $mockRepository->shouldReceive('revokeGroupRole')->with($role, $group)->once()->andReturn(true);

        $object = new Acl($mockRepository);
        $response = $object->revokeGroupRole($role, $group);
        $this->assertTrue($response);
    }
}
