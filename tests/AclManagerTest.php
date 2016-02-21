<?php

namespace Signes\Acl;

use Mockery;
use ReflectionClass;
use Signes\Acl\Contract\AclRepository;
use Signes\Acl\Contract\RoleInterface;
use Signes\Acl\Contract\UserInterface;

/**
 * Class AclTest
 * @coversDefaultClass \Signes\Acl\AclManager
 *
 * @group signes_acl
 */
class AclManagerTest extends TestCase
{
    /**
     * @covers ::__construct
     */
    public function testConstructorWithRepository()
    {
        $mockRepository = Mockery::mock(AclRepository::class);
        $object = $object = $this->getMockForTest($mockRepository);

        $repository = (new ReflectionClass($object))->getProperty('repository');
        $repository->setAccessible(true);

        $this->assertEquals($mockRepository, $repository->getValue($object));
    }

    /**
     * @param AclRepository $mockRepository
     * @return AclManager
     */
    protected function getMockForTest(AclRepository $mockRepository = null)
    {
        if ($mockRepository === null) {
            $mockRepository = Mockery::mock(AclRepository::class);
        }

        return Mockery::mock(Acl::class, [$mockRepository])->makePartial()->shouldAllowMockingProtectedMethods();
    }

    /**
     * Test namespace setter
     * @covers ::setSiteNamespace
     */
    public function testSetNamespaces()
    {
        $namespace = 'namespace';
        $mockRepository = Mockery::mock(AclRepository::class);
        $mockRepository->shouldReceive('setSiteNamespace')->with($namespace)->once()->andReturn(true);

        $object = $this->getMockForTest($mockRepository);
        $response = $object->setSiteNamespace($namespace);
        $this->assertTrue($response);
    }

    /**
     * Ensure we can set user object and another set will not fallback to guest account.
     * @covers ::ensureUser
     * @covers ::setUser
     * @covers ::getUser
     */
    public function testEnsureUser()
    {
        $mockUser = Mockery::mock(UserInterface::class);

        $object = $this->getMockForTest();

        $object->ensureUser($mockUser);
        $this->assertEquals($mockUser, $object->getUser());

        $object->ensureUser();
        $this->assertEquals($mockUser, $object->getUser());
    }

    /**
     * Ensure without any object, when user is not set yet, should set Guest account
     * @covers ::ensureUser
     * @covers ::setUser
     * @covers ::getUser
     */
    public function testEnsureUserGuest()
    {
        $mockUser = Mockery::mock(UserInterface::class);

        $mockRepository = Mockery::mock(AclRepository::class);
        $mockRepository->shouldReceive('getGuest')->withNoArgs()->once()->andReturn($mockUser);

        $object = $this->getMockForTest($mockRepository);
        $object->ensureUser();
        $this->assertEquals($mockUser, $object->getUser());
    }

    /**
     * @covers ::prepareResource
     * @dataProvider dataPrepareResource
     * @param string $resource
     * @param array $expectedResult
     */
    public function testPrepareResource($resource, $expectedResult)
    {
        $object = $this->getMockForTest();
        $this->assertEquals($expectedResult, $object->prepareResource($resource));
    }

    /**
     * Data for test `testPrepareResource`
     *
     * @return array
     */
    public function dataPrepareResource()
    {
        return [
            ['', ['area' => null, 'permission' => null, 'actions' => null]],
            ['area1.permission1', ['area' => 'area1', 'permission' => 'permission1', 'actions' => null]],
            ['.permission1', ['area' => '', 'permission' => 'permission1', 'actions' => null]],
            ['area1.', ['area' => 'area1', 'permission' => '', 'actions' => null]],
            [
                'area1.permission1|action1',
                ['area' => 'area1', 'permission' => 'permission1', 'actions' => ['action1']]
            ],
            [
                'area1.permission1|action1.action2.action3',
                ['area' => 'area1', 'permission' => 'permission1', 'actions' => ['action1', 'action2', 'action3']]
            ],
            [
                'area1.permission1|action1.action1.action2',
                ['area' => 'area1', 'permission' => 'permission1', 'actions' => ['action1', 'action1', 'action2']]
            ],
        ];
    }

    /**
     * Test mechanism to compare resource with permissions
     * @covers ::compareResourceWithPermissions
     * @covers ::isActionRevoked
     *
     * @dataProvider dataCompareResourceWithPermissions
     */
    public function testCompareResourceWithPermissions($resourceMap, $permission, $expectedResult)
    {
        $object = $this->getMockForTest();
        $this->assertEquals($expectedResult, $object->compareResourceWithPermissions($resourceMap, $permission));
    }

    /**
     * Data for test `testCompareResourceWithPermissions`
     *
     * @return array
     */
    public function dataCompareResourceWithPermissions()
    {
        return [
            [[], ['_special.root' => true], true],
            [[], ['_special.deny' => true], false],
            [[], ['_special.deny' => true, '_special.root' => true], true],
            [
                ['area' => 'ar2', 'permission' => 'p2', 'actions' => []],
                ['ar1' => ['p1' => []]],
                false
            ],
            [
                ['area' => 'ar1', 'permission' => 'p1', 'actions' => []],
                ['ar1' => ['p1' => []]],
                true
            ],
            [
                ['area' => 'ar1', 'permission' => 'p1', 'actions' => ['a1', 'a2', 'a3']],
                ['ar1' => ['p1' => ['a1', 'a2', 'a3']]],
                true
            ],
            [
                ['area' => 'ar1', 'permission' => 'p1', 'actions' => ['a1', 'a2', 'a3']],
                ['ar1' => ['p1' => ['a1', 'a2', 'a3', 'a4']]],
                true
            ],
            [
                ['area' => 'ar1', 'permission' => 'p1', 'actions' => ['a1', 'a2', 'a3']],
                ['ar1' => ['p1' => ['a1', 'a3']]],
                false
            ],
            [
                ['area' => 'ar1', 'permission' => 'p1', 'actions' => ['a1', 'a2']],
                ['ar1' => ['p1' => ['a1', 'a2']], '_special' => ['removed' => ['ar1' => ['p1' => ['a1']]]]],
                false
            ]
        ];
    }

    /**
     * Test special roles for access like all granted or all revoked
     * @covers ::parseSpecialRoles
     */
    public function testParseSpecialRoleDeny()
    {
        $object = $this->getMockForTest();

        $mockRoleDeny = Mockery::mock(RoleInterface::class);
        $mockRoleDeny->shouldReceive('getFilter')->once()->andReturn('D');

        $mockRoleAllow = Mockery::mock(RoleInterface::class);
        $mockRoleAllow->shouldReceive('getFilter')->once()->andReturn('A');

        $permissions = $object->parseSpecialRoles($mockRoleDeny, []);
        $permissions = $object->parseSpecialRoles($mockRoleAllow, $permissions);

        $this->assertArrayHasKey('_special', $permissions);
        $this->assertArrayHasKey('deny', $permissions['_special']);
        $this->assertArrayHasKey('root', $permissions['_special']);
        $this->assertTrue($permissions['_special']['deny']);
        $this->assertTrue($permissions['_special']['root']);
    }
}
