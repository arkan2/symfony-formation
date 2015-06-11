<?php

namespace AppBundle\Tests\User;

use AppBundle\User\User;
use AppBundle\User\UserAlreadyExistsException;
use AppBundle\User\UserManager;

class UserManagerTest extends \PHPUnit_Framework_TestCase
{
    private static $dbPath;

    protected function setUp()
    {
        self::$dbPath = __DIR__.'/../Fixtures/database';
    }

    private function createManager()
    {
        return new UserManager(self::$dbPath);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testUserExistsButNotReadable()
    {
        $manager = $this->createManager();
        $manager->findByUsername('notreadable');
    }

    public function testUserDoesNotExist()
    {
        $manager = $this->createManager();

        $this->assertFalse($manager->findByUsername('toto'));
    }

    /**
     * @dataProvider provideUsername
     */
    public function testFindUser($username)
    {
        $manager = $this->createManager();

        $this->assertInstanceOf(
            'AppBundle\User\User',
            $manager->findByUsername($username)
        );
    }

    public function provideUsername()
    {
        return [
            [ 'jsmith' ],
            [ 'JSMITH' ],
            [ 'jsMitH' ],
        ];
    }

    public function testSaveUsersWithSameUsernamesButDifferentCases()
    {
        $user1 = new User();
        $user1->setUsername('hhamon');

        $user2 = new User();
        $user2->setUsername('HHAMON');

        $manager = $this->createManager();
        $manager->save($user1);

        try {
            $manager->save($user2);
            $this->fail();
        } catch (UserAlreadyExistsException $e) {
            $this->assertFileNotExists(self::$dbPath.'/HHAMON.txt');
            @unlink(self::$dbPath.'/hhamon.txt');
        }
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testUnableToSaveUserOnDisk()
    {
        if ('\\' === DIRECTORY_SEPARATOR) {
            $this->markTestSkipped('A Linux/Unix OS is required to run this test.');
        }

        $user = new User();
        $user->setUsername('foobar');

        $manager = $this->createManager();
        $manager->save($user);
    }

    public function testSaveUserOnDisk()
    {
        $user = new User();
        $user->setUsername('hhamon');

        $manager = $this->createManager();
        $manager->save($user);

        $path = self::$dbPath.'/hhamon.txt';

        $this->assertFileExists($path);
        $this->assertSame(serialize($user), file_get_contents($path));

        @unlink($path);
    }
}
