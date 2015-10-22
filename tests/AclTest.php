<?php

namespace {

    use App\Models\Acl\Group;
    use App\Models\Acl\Role;
    use App\Models\Acl\User;
    use Signes\Acl\Acl;
    use Signes\Acl\Repository\SignesAclRepository;

    /**
     * Class AclTest
     * @coversDefaultClass \Signes\Acl\Acl
     *
     * @group signes_acl
     */
    class AclTest extends TestCase
    {
        /**
         * @var Acl
         */
        protected $acl;

        /**
         * Set up repository to tests
         */
        public function setUp()
        {
            parent::setUp();

            $this->acl = $this->setUpAclSignes();
        }

        /**
         * Set up ACL with Signes provider
         *
         * @return Acl
         */
        protected function setUpAclSignes()
        {
            return new Acl(new SignesAclRepository());
        }

        /**
         * Test namespace setter
         * @covers ::setSiteNamespace
         */
        public function testNamespace()
        {
            $name = 'Test';
            $this->acl->setSiteNamespace($name);
            $repository = PHPUnit_Framework_Assert::readAttribute($this->acl, 'repository');
            $this->assertEquals($name, PHPUnit_Framework_Assert::readAttribute($repository, 'appNamespace'));
        }

        /**
         * Test namespace setter
         * @covers ::setUser
         */
        public function testSetUser()
        {
            $user = new User();
            $user->login = 'TestUser';
            $user->password = 'TestPassword';
            $user->group_id = '2';
            $user->save();

            $this->acl->setUser($user);
            $user = PHPUnit_Framework_Assert::readAttribute($this->acl, 'currentUser');

            $this->assertEquals('TestUser', $user->login);
            $this->assertEquals(2, $user->group_id);
        }

        /**
         * @covers ::createPermission
         * @covers ::grantUserPermission
         * @covers ::grantGroupPermission
         * @covers ::grantRolePermission
         * @covers ::grantGroupRole
         * @covers ::grantUserRole
         * @covers ::collectPermissions
         * @covers ::collectUserPermissions
         * @covers ::collectGroupPermissions
         * @covers ::collectRolePermission
         * @covers ::parseSpecialRoles
         * @covers ::parsePermissions
         * @covers ::__prepareResource
         * @covers ::__compareResourceWithPermissions
         * @covers ::isAllow
         */
        public function testAclAreaPermission()
        {
            $group = new Group();
            $group->setName('TestGroup')->save();

            $role1 = new Role();
            $role1->setName('TestRole1')->save();

            $role2 = new Role();
            $role2->setName('TestRole2')->save();

            $role3 = new Role();
            $role3->setName('TestRole3')->setFilter('R')->save();

            $permission1 = $this->acl->createPermission('zone1', 'access1', ['act1', 'act2', 'act3'], 'Zone 1');
            $permission2 = $this->acl->createPermission('zone2', 'access2', ['act1', 'act2', 'act3'], 'Zone 2');
            $permission3 = $this->acl->createPermission('zone3', 'access3', ['act1', 'act2', 'act3'], 'Zone 3');

            $user = new User();
            $user->login = 'TestUser';
            $user->password = 'TestPassword';
            $user->setGroup($group);
            $user->save();

            // Connect user with permissions
            $this->acl->grantUserPermission($permission1, $user, ['act1'], true);
            $this->acl->grantUserPermission($permission2, $user, ['act1', 'act2', 'act3'], true);

            // Connect group with permissions
            $this->acl->grantGroupPermission($permission1, $group, ['act3'], true);

            // Connect roles with permissions
            $this->acl->grantRolePermission($permission3, $role1, ['act1'], true);
            $this->acl->grantRolePermission($permission1, $role2, ['act2'], true);
            $this->acl->grantRolePermission($permission2, $role3, ['act2'], true);

            // Connect user with roles
            $this->acl->grantUserRole($role2, $user, true);
            $this->acl->grantUserRole($role3, $user, true);

            // Connect group with roles
            $this->acl->grantGroupRole($role1, $group, true);
            $this->acl->grantGroupRole($role2, $group, true);

            $this->assertTrue($this->acl->isAllow('zone1.access1|act1.act2.act3', $user));
            $this->assertTrue($this->acl->isAllow('zone2.access2|act1.act3', $user));
            $this->assertTrue($this->acl->isAllow('zone3.access3|act1', $user));

            // because act2 was revoked (R)
            $this->assertFalse($this->acl->isAllow('zone2.access2|act1.act2.act3', $user));
        }
    }
}
