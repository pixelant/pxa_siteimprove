<?php
namespace Pixelant\PxaSiteimprove\Tests\Unit\Domain\Model;

/**
 * Test case.
 *
 * @author Mattias Nilsson <mattias@pixelant.se>
 */
class DashboardTest extends \TYPO3\CMS\Core\Tests\UnitTestCase
{
    /**
     * @var \Pixelant\PxaSiteimprove\Domain\Model\Dashboard
     */
    protected $subject = null;

    protected function setUp()
    {
        parent::setUp();
        $this->subject = new \Pixelant\PxaSiteimprove\Domain\Model\Dashboard();
    }

    protected function tearDown()
    {
        parent::tearDown();
    }

    /**
     * @test
     */
    public function getPageReturnsInitialValueForString()
    {
        self::assertSame(
            '',
            $this->subject->getPage()
        );
    }

    /**
     * @test
     */
    public function setPageForStringSetsPage()
    {
        $this->subject->setPage('Conceived at T3CON10');

        self::assertAttributeEquals(
            'Conceived at T3CON10',
            'page',
            $this->subject
        );
    }

    /**
     * @test
     */
    public function getTokenReturnsInitialValueForString()
    {
        self::assertSame(
            '',
            $this->subject->getToken()
        );
    }

    /**
     * @test
     */
    public function setTokenForStringSetsToken()
    {
        $this->subject->setToken('Conceived at T3CON10');

        self::assertAttributeEquals(
            'Conceived at T3CON10',
            'token',
            $this->subject
        );
    }
}
