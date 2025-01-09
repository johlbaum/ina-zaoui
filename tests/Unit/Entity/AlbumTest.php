<?php

namespace App\Tests\Entity;

use App\Entity\Album;
use PHPUnit\Framework\TestCase;

class AlbumTest extends TestCase
{
    public function testCreateAlbum(): void
    {
        $album = new Album();

        $this->assertInstanceOf(Album::class, $album);
    }

    public function testGetSetName(): void
    {
        $album = new Album();

        $album->setName('Album 1');
        $this->assertEquals('Album 1', $album->getName());
    }

    public function testIsEmpty(): void
    {
        $album = new Album();

        $this->assertNull($album->getId());
    }
}
