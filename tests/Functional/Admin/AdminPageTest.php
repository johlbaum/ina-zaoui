<?php

namespace App\Tests\Functional\Home\Admin;

use App\Tests\Functional\FunctionalTestCase;

class AdminPageTest extends FunctionalTestCase
{
    public function testShouldDisplayAdminPageForAdmin(): void
    {
        $this->login();

        $this->client->request('GET', '/admin');
        $this->assertResponseIsSuccessful();

        $this->assertSelectorExists('.nav-item a.nav-link:contains("Medias")');
        $this->assertSelectorExists('.nav-item a.nav-link:contains("Albums")');
        $this->assertSelectorExists('.nav-item a.nav-link:contains("Invités")');
    }

    public function testShouldDisplayAdminPageForGuestWithLimitedAccess(): void
    {
        $this->login('invite1@example.com');

        $this->client->request('GET', '/admin');
        $this->assertResponseIsSuccessful();

        $this->assertSelectorTextContains('.nav-item a.nav-link', 'Medias');
        $this->assertSelectorNotExists('.nav-item a.nav-link:contains("Albums")');
        $this->assertSelectorNotExists('.nav-item a.nav-link:contains("Invités")');
    }
}
