<?php

namespace App\Tests\Functional\Home\About;

use App\Tests\Functional\FunctionalTestCase;

class ShowAboutTest extends FunctionalTestCase
{
    public function testAboutPageIsAccessible(): void
    {
        $this->get('/about');
        $this->assertResponseIsSuccessful();

        $this->assertSelectorTextContains('h2', 'Qui suis-je ?');
    }
}
