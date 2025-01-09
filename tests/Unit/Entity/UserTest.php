<?php

namespace App\Tests;

use App\Entity\User;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    public function testSetNameAndGetName(): void
    {
        $user = new User();

        $user->setName('Invité 1');
        $this->assertEquals('Invité 1', $user->getName());
    }

    public function testSetEmailAndGetEmail(): void
    {
        $user = new User();

        $user->setEmail('invite.test@test.com');
        $this->assertEquals('invite.test@test.com', $user->getEmail());
    }

    public function testSetDescriptionAndGetDescription(): void
    {
        $user = new User();

        $user->setDescription('Une description');
        $this->assertEquals('Une description', $user->getDescription());
    }

    public function testSetPasswordAndGetPassword(): void
    {
        $user = new User();

        $user->setPassword('password');
        $this->assertEquals('password', $user->getPassword());
    }

    public function testSetRolesAndGetRoles(): void
    {
        $user = new User();

        $user->setRoles(['ROLE_USER']);
        $this->assertEquals(['ROLE_USER'], $user->getRoles());
    }

    public function testSetUserAccessEnabledAndGetUserAccessEnabled(): void
    {
        $user = new User();

        $user->setUserAccessEnabled(true);
        $this->assertTrue($user->isUserAccessEnabled());

        $user->setUserAccessEnabled(false);
        $this->assertFalse($user->isUserAccessEnabled());
    }

    public function testIsEmpty(): void
    {
        $user = new User();

        $this->assertEmpty($user->getId());
    }

    public function testSetMediasAndGetMedias(): void
    {
        $user = new User();

        $mediaCollection = new ArrayCollection(['media1', 'media2', 'media3']);
        $user->setMedias($mediaCollection);
        $this->assertEquals($mediaCollection, $user->getMedias());
    }

    public function testGetUserIdentifier(): void
    {
        $user = new User();

        $user->setName('Invité 1');
        $this->assertEquals('Invité 1', $user->getUserIdentifier());
    }
}
