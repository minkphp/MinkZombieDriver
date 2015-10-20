<?php

namespace Behat\Mink\Tests\Driver\Custom;

use Behat\Mink\Tests\Driver\TestCase;

/**
 * @group zombiedriver
 */
class ExtraTest extends TestCase
{
    // TODO move upstream
    public function testSetUserAgent()
    {
        $session = $this->getSession();

        $session->setRequestHeader('user-agent', 'foo bar');
        $session->visit($this->pathTo('/headers.php'));
        $this->assertContains('foo bar', $session->getPage()->getText());
    }

    // TODO check whether this is covered by upstream test
    public function testSetRequestHeader()
    {
        $this->getSession()->setRequestHeader('foo', 'bar');
        $this->getSession()->visit($this->pathTo('/headers.php'));
        $this->assertContains('[HTTP_FOO] => bar', $this->getSession()->getPage()->getText());
    }

    public function testSessionResetSwitchesToMainWindow()
    {
        $this->getSession()->visit($this->pathTo('/window.html'));
        $session = $this->getSession();
        $page = $session->getPage();
        $webAssert = $this->getAssertSession();

        $page->clickLink('Popup #1');
        $session->switchToWindow('popup_1');
        $session->reset();

        $el = $webAssert->elementExists('css', '#text');
        $this->assertSame('Main window div text', $el->getText());
    }
}
