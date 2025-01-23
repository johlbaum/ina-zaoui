<?php

namespace App\Tests\Functional\Home;

use App\Tests\Functional\FunctionalTestCase;

class HomePageTest extends FunctionalTestCase
{
    public function testHomePageIsSuccessful(): void
    {
        $this->get('/');
        $this->assertResponseIsSuccessful();

        $this->assertSelectorTextContains('h2', 'Photographe');
    }
}
