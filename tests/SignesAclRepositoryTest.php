<?php

namespace {

    use App\Models\Acl\Group;
    use App\Models\Acl\Permission;
    use App\Models\Acl\Role;
    use App\Models\Acl\User;
    use Signes\Acl\PermissionInterface;
    use Signes\Acl\Repository\SignesAclRepository;

    /**
     * Class SignesAclRepositoryTest
     * @coversDefaultClass \Signes\Acl\Repository\SignesAclRepository
     *
     * @group signes_acl_provider_signes
     */
    class SignesAclRepositoryTest extends TestCase
    {
        /**
         * Expected values fot guest object
         *
         * @var array
         */
        private $expectedGuest = [
            'id'       => 1,
            'login'    => 'guest',
            'password' => 'NO PASSWORD',
            'group_id' => 2,
        ];

        /**
         * @var SignesAclRepository
         */
        protected $repository;

        /**
         * Set up repository to tests
         */
        public function setUp()
        {
            parent::setUp();

            $this->repository = new SignesAclRepository();
        }

        /**
         * Test namespace setter
         * @covers ::setSiteNamespace
         */
        public function testNamespace()
        {
            $name = 'Test';
            $this->repository->setSiteNamespace($name);
            $this->assertEquals($name, PHPUnit_Framework_Assert::readAttribute($this->repository, 'appNamespace'));
        }

        /**
         * Test guest object data
         * @covers ::getGuest
         * @covers ::getModelName
         */
        public function testGetGuest()
        {
            $guestObject = $this->repository->getGuest();

            $this->assertEquals($this->expectedGuest['id'], $guestObject->id);
            $this->assertEquals($this->expectedGuest['login'], $guestObject->login);
            $this->assertEquals($this->expectedGuest['password'], $guestObject->password);
            $this->assertEquals($this->expectedGuest['group_id'], $guestObject->group_id);
        }

        /**
         * @param $area
         * @param $permission
         * @param $actions
         * @param $description
         * @dataProvider permissionsDataProvider
         * @covers ::createPermission
         * @covers ::getModelName
         */
        public function testCreatePermission($area, $permission, $actions, $description)
        {
            $result1 = $this->repository->createPermission($area, $permission, $actions, $description);
            $this->assertTrue($result1 instanceof PermissionInterface);
            $result2 = $this->repository->createPermission($area, $permission, $actions, $description);
            $this->assertFalse($result2);
        }

        /**
         * @covers ::deletePermission
         * @covers ::createPermission
         */
        public function testDeletePermissionsByArea()
        {
            $this->repository->createPermission('area1', 'permission1', []);
            $this->repository->createPermission('area1', 'permission2', ['action1', 'action2']);
            $this->repository->createPermission('area2', 'permission1', ['action1', 'action2']);

            $this->assertEquals(2, $this->repository->deletePermission('area1'));
        }

        /**
         * @covers ::deletePermission
         * @covers ::createPermission
         */
        public function testDeletePermissionsByPermission()
        {
            $this->repository->createPermission('area1', 'permission1', []);
            $this->repository->createPermission('area1', 'permission2', ['action1', 'action2']);
            $this->repository->createPermission('area2', 'permission1', ['action1', 'action2']);

            $this->assertEquals(1, $this->repository->deletePermission('area1', 'permission1'));
        }

        /**
         * @covers ::deletePermission
         * @covers ::createPermission
         */
        public function testDeletePermissionsByActions()
        {
            $this->repository->createPermission('area1', 'permission1', ['action1', 'action2', 'action3', 'action4']);
            $this->repository->deletePermission('area1', 'permission1', ['action2', 'action4']);

            $actions = Permission::where('area', '=', 'area1')
                ->where('permission', '=', 'permission1')->first()->getActions();

            $this->assertCount(2, $actions);
            $this->assertContains('action1', $actions);
            $this->assertContains('action3', $actions);
        }

        /**
         * @covers ::deletePermission
         */
        public function testDeleteWrongSyntax()
        {
            $this->assertFalse($this->repository->deletePermission('area1', null, []));
            $this->assertFalse($this->repository->deletePermission('area1', 'permission1', []));
        }

        /**
         * @covers ::createPermission
         * @covers ::grantUserPermission
         * @covers ::revokeUserPermission
         * @covers ::grantEntityPermission
         */
        public function testUserPermissionGrantRevoke()
        {
            $user = new User();
            $user->login = 'TestUser';
            $user->password = 'TestPassword';
            $user->group_id = '2';
            $user->save();

            $permission = $this->repository->createPermission('area1', 'permission1', ['access1', 'access2']);

            $this->repository->grantUserPermission($permission, $user);
            $this->assertEquals(1, $user->getPermissions()->get()->count());

            $this->assertTrue($this->repository->revokeUserPermission($permission, $user));
            $this->assertEquals(0, $user->getPermissions()->get()->count());
        }

        /**
         * @covers ::createPermission
         * @covers ::grantUserPermission
         * @covers ::grantEntityPermission
         * @throws \Signes\Acl\Exception\DuplicateEntry
         * @expectedException \Signes\Acl\Exception\DuplicateEntry
         */
        public function testUserPermissionGrantDuplicate()
        {
            $user = new User();
            $user->login = 'TestUser';
            $user->password = 'TestPassword';
            $user->group_id = '2';
            $user->save();

            $permission = $this->repository->createPermission('area1', 'permission1', ['access1', 'access2']);

            $this->repository->grantUserPermission($permission, $user);
            $this->repository->grantUserPermission($permission, $user);
        }

        /**
         * @covers ::createPermission
         * @covers ::grantGroupPermission
         * @covers ::revokeGroupPermission
         * @covers ::grantEntityPermission
         */
        public function testGroupPermissionGrantRevoke()
        {
            $group = new Group();
            $group->setName('TestGroup')->save();

            $permission = $this->repository->createPermission('area1', 'permission1', ['access1', 'access2']);

            $this->repository->grantGroupPermission($permission, $group);
            $this->assertEquals(1, $group->getPermissions()->get()->count());

            $this->assertTrue($this->repository->revokeGroupPermission($permission, $group));
            $this->assertEquals(0, $group->getPermissions()->get()->count());
        }

        /**
         * @covers ::createPermission
         * @covers ::grantGroupPermission
         * @covers ::grantEntityPermission
         * @throws \Signes\Acl\Exception\DuplicateEntry
         * @expectedException \Signes\Acl\Exception\DuplicateEntry
         */
        public function testGroupPermissionGrantDuplicate()
        {
            $group = new Group();
            $group->setName('TestGroup')->save();

            $permission = $this->repository->createPermission('area1', 'permission1', ['access1', 'access2']);

            $this->repository->grantGroupPermission($permission, $group);
            $this->repository->grantGroupPermission($permission, $group);
        }

        /**
         * @covers ::createPermission
         * @covers ::grantRolePermission
         * @covers ::revokeRolePermission
         * @covers ::grantEntityPermission
         */
        public function testRolePermissionGrantRevoke()
        {
            $role = new Role();
            $role->setName('TestRole')->save();

            $permission = $this->repository->createPermission('area1', 'permission1', ['access1', 'access2']);

            $this->repository->grantRolePermission($permission, $role);
            $this->assertEquals(1, $role->getPermissions()->get()->count());

            $this->assertTrue($this->repository->revokeRolePermission($permission, $role));
            $this->assertEquals(0, $role->getPermissions()->get()->count());
        }

        /**
         * @covers ::createPermission
         * @covers ::grantRolePermission
         * @covers ::grantEntityPermission
         * @throws \Signes\Acl\Exception\DuplicateEntry
         * @expectedException \Signes\Acl\Exception\DuplicateEntry
         */
        public function testRolePermissionGrantDuplicate()
        {
            $role = new Role();
            $role->setName('TestRole')->save();

            $permission = $this->repository->createPermission('area1', 'permission1', ['access1', 'access2']);

            $this->repository->grantRolePermission($permission, $role);
            $this->repository->grantRolePermission($permission, $role);
        }

        /**
         * @covers ::grantUserRole
         * @covers ::revokeUserRole
         */
        public function testUserRoleGrantRevoke()
        {
            $user = new User();
            $user->login = 'TestUser';
            $user->password = 'TestPassword';
            $user->group_id = '2';
            $user->save();

            $role = new Role();
            $role->setName('TestRole')->save();

            $this->repository->grantUserRole($role, $user);
            $this->assertEquals(1, $user->getRoles()->get()->count());
            $this->assertEquals('TestRole', $user->getRoles()->get()->first()->getName());

            $this->assertTrue($this->repository->revokeUserRole($role, $user));
            $this->assertEquals(0, $user->getPermissions()->get()->count());
        }

        /**
         * @covers ::grantUserRole
         * @covers ::revokeUserRole
         * @throws \Signes\Acl\Exception\DuplicateEntry
         * @expectedException \Signes\Acl\Exception\DuplicateEntry
         */
        public function testUserRoleGrantDuplicate()
        {
            $user = new User();
            $user->login = 'TestUser';
            $user->password = 'TestPassword';
            $user->group_id = '2';
            $user->save();

            $role = new Role();
            $role->setName('TestRole')->save();

            $this->repository->grantUserRole($role, $user);
            $this->repository->grantUserRole($role, $user);
        }

        /**
         * @covers ::grantGroupRole
         * @covers ::revokeGroupRole
         */
        public function testGroupRoleGrantRevoke()
        {
            $group = new Group();
            $group->setName('TestGroup')->save();

            $role = new Role();
            $role->setName('TestRole')->save();

            $this->repository->grantGroupRole($role, $group);
            $this->assertEquals(1, $group->getRoles()->get()->count());
            $this->assertEquals('TestRole', $group->getRoles()->get()->first()->getName());

            $this->assertTrue($this->repository->revokeGroupRole($role, $group));
            $this->assertEquals(0, $group->getPermissions()->get()->count());
        }

        /**
         * @covers ::grantGroupRole
         * @covers ::revokeGroupRole
         * @throws \Signes\Acl\Exception\DuplicateEntry
         * @expectedException \Signes\Acl\Exception\DuplicateEntry
         */
        public function testGroupRoleGrantDuplicate()
        {
            $group = new Group();
            $group->setName('TestGroup')->save();

            $role = new Role();
            $role->setName('TestRole')->save();

            $this->repository->grantGroupRole($role, $group);
            $this->repository->grantGroupRole($role, $group);
        }

        /**
         * Data provider with permissions
         *
         * @return array
         */
        public function permissionsDataProvider()
        {
            return [
                ['area1', 'permission1', 'action1', 'description1'],
                ['area2', 'permission2', null, 'description1'],
                ['area3', 'permission3', null, null],
                ['area4', 'permission4', ['action41', 'action42'], 'description4'],
            ];
        }
    }
}
