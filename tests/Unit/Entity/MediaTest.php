<?php

namespace App\Tests\Unit\Entity;

use App\Entity\Album;
use App\Entity\Media;
use App\Entity\User;
use PHPUnit\Framework\TestCase;

class MediaTest extends TestCase
{
    public function testSetAndGetUser(): void
    {
        $user = new User();
        $media = new Media();

        $media->setUser($user);
        $this->assertSame($user, $media->getUser());
    }

    public function testSetAndGetTitle(): void
    {
        $media = new Media();

        $media->setTitle('Un titre');
        $this->assertEquals('Un titre', $media->getTitle());
    }

    public function testSetAndGetPath(): void
    {
        $media = new Media();

        $media->setPath('/uploads/image.jpg');
        $this->assertEquals('/uploads/image.jpg', $media->getPath());
    }

    public function testSetAndGetAlbum(): void
    {
        $album = new Album();
        $media = new Media();

        $media->setAlbum($album);
        $this->assertSame($album, $media->getAlbum());
    }

    public function testIsEmpty(): void
    {
        $media = new Media();

        $this->assertEmpty($media->getId());
    }
}
