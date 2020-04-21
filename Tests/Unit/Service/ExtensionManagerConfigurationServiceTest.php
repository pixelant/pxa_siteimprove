<?php

namespace Pixelant\PxaSiteimprove\Tests\Unit\Service;

/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

use Pixelant\PxaSiteimprove\Service\ExtensionManagerConfigurationService;
use Pixelant\PxaSiteimprove\TestCase\UnitTestCase;

/**
 * Test case for class Pixelant\PxaSiteimprove\Service\ExtensionManagerConfigurationService.
 */
class ExtensionManagerConfigurationServiceTest extends UnitTestCase
{
    protected function setUp()
    {
        parent::setUp();

        $GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS']['pxa_siteimprove'] = [];
    }


    /**
     * @test
     */
    public function getExtensionManagerSettings()
    {
        $this->assertSame(
            ExtensionManagerConfigurationService::getSettings(),
            []
        );
    }
}
