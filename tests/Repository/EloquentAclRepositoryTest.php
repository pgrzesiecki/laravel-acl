<?php

namespace Signes\Acl\Repository;

use App\Models\Acl\Group;
use App\Models\Acl\Permission;
use App\Models\Acl\Role;
use App\Models\Acl\User;
use Orchestra\Testbench\TestCase;
use ReflectionClass;
use Signes\Acl\Acl;
use Signes\Acl\Contract\PermissionInterface;

/**
 * Class EloquentAclRepositoryTest
 * @coversDefaultClass \Signes\Acl\Repository\EloquentAclRepository
 *
 * @package Signes\Acl\Repository
 * @group   signes_acl
 */
class EloquentAclRepositoryTest extends TestCase
{
    /**
     * @var EloquentAclRepository
     */
    protected $repository;

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
     * Set up repository to tests
     */
    public function setUp()
    {
        parent::setUp();

        $this->artisan(
            'migrate',
            [
                '--database' => 'sqlite',
                '--realpath' => realpath(__DIR__ . '/../../src/Publish/Migrations'),
            ]
        );

        $this->repository = $this->setUpAclEloquent();
    }

    /**
     * Tear down environment after tests
     */
    public function tearDown()
    {
        $this->artisan(
            'migrate:rollback',
            [
                '--database' => 'sqlite'
            ]
        );
        parent::tearDown();
    }


    /**
     * @param \Illuminate\Foundation\Application $app
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return ['Signes\Acl\AclServiceProvider'];
    }

    /**
     * @param \Illuminate\Foundation\Application $app
     * @return array
     */
    protected function getPackageAliases($app)
    {
        return [
            'Acl' => 'Signes\Acl\Facades\Acl'
        ];
    }

    /**
     * Define environment setup.
     *
     * @param  \Illuminate\Foundation\Application $app
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
        // reset base path to point to our package's src directory
        $config = require __DIR__ . '/../config/database.php';
        $app['config']->set('path.storage', '/');
        $app['config']->set('database.default', 'sqlite');
        $app['config']->set('database.connections.sqlite', $config['connections']['sqlite']);
        $app['config']->set('cache.driver', 'array');
    }

    /**
     * Set up ACL with Signes provider
     *
     * @return EloquentAclRepository
     */
    protected function setUpAclEloquent()
    {
        return new EloquentAclRepository();
    }

    /**
     * Test namespace setter
     * @covers ::setSiteNamespace
     */
    public function testNamespace()
    {
        $name = 'Test';
        $this->repository->setSiteNamespace($name);

        $namespace = (new ReflectionClass($this->repository))->getProperty('appNamespace');
        $namespace->setAccessible(true);

        $this->assertEquals($name, $namespace->getValue($this->repository));
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
     * @dataProvider dataCreatePermission
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
     * Data provider with permissions
     *
     * @return array
     */
    public function dataCreatePermission()
    {
        return [
            ['area1', 'permission1', 'action1', 'description1'],
            ['area2', 'permission2', null, 'description1'],
            ['area3', 'permission3', null, null],
            ['area4', 'permission4', ['action41', 'action42'], 'description4'],
        ];
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
        $this->assertEquals(1, $this->repository->deletePermission('area2', 'permission1'));
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
            ->where('permission', '=', 'permission1')
            ->first()
            ->getActions();

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
     * @throws \Signes\Acl\Exception\DuplicateEntryException
     * @expectedException \Signes\Acl\Exception\DuplicateEntryException
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
     * @throws \Signes\Acl\Exception\DuplicateEntryException
     * @expectedException \Signes\Acl\Exception\DuplicateEntryException
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
     * @throws \Signes\Acl\Exception\DuplicateEntryException
     * @expectedException \Signes\Acl\Exception\DuplicateEntryException
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
     * @throws \Signes\Acl\Exception\DuplicateEntryException
     * @expectedException \Signes\Acl\Exception\DuplicateEntryException
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
     * @throws \Signes\Acl\Exception\DuplicateEntryException
     * @expectedException \Signes\Acl\Exception\DuplicateEntryException
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
     * @covers ::createPermission
     * @covers ::grantUserPermission
     * @covers ::grantGroupPermission
     * @covers ::grantRolePermission
     * @covers ::grantGroupRole
     * @covers ::grantUserRole
     */
    public function testAclAreaPermission()
    {
        $acl = new Acl($this->repository);

        $group = new Group();
        $group->setName('TestGroup')->save();

        $role1 = new Role();
        $role1->setName('TestRole1')->save();

        $role2 = new Role();
        $role2->setName('TestRole2')->save();

        $role3 = new Role();
        $role3->setName('TestRole3')->setFilter('R')->save();

        $permission1 = $acl->createPermission('zone1', 'access1', ['act1', 'act2', 'act3'], 'Zone 1');
        $permission2 = $acl->createPermission('zone2', 'access2', ['act1', 'act2', 'act3'], 'Zone 2');
        $permission3 = $acl->createPermission('zone3', 'access3', ['act1', 'act2', 'act3'], 'Zone 3');

        $user = new User();
        $user->login = 'TestUser';
        $user->password = 'TestPassword';
        $user->setGroup($group);
        $user->save();

        // Connect user with permissions
        $acl->grantUserPermission($permission1, $user, ['act1'], true);
        $acl->grantUserPermission($permission2, $user, ['act1', 'act2', 'act3'], true);

        // Connect group with permissions
        $acl->grantGroupPermission($permission1, $group, ['act3'], true);

        // Connect roles with permissions
        $acl->grantRolePermission($permission3, $role1, ['act1'], true);
        $acl->grantRolePermission($permission1, $role2, ['act2'], true);
        $acl->grantRolePermission($permission2, $role3, ['act2'], true);

        // Connect user with roles
        $acl->grantUserRole($role2, $user, true);
        $acl->grantUserRole($role3, $user, true);

        // Connect group with roles
        $acl->grantGroupRole($role1, $group, true);
        $acl->grantGroupRole($role2, $group, true);

        $this->assertTrue($acl->isAllow('zone1.access1|act1.act2.act3', $user));
        $this->assertTrue($acl->isAllow('zone2.access2|act1.act3', $user));
        $this->assertTrue($acl->isAllow('zone3.access3|act1', $user));

        // because act2 was revoked (R)
        $this->assertFalse($acl->isAllow('zone2.access2|act1.act2.act3', $user));
    }
}
