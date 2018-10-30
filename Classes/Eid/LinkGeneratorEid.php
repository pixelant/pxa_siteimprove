<?php

$settings = \Pixelant\PxaSiteimprove\Service\ExtensionManagerConfigurationService::getSettings();

/** @var \Pixelant\PxaSiteimprove\Service\PageUrlEidService $pagePathResolverService */
$pagePathResolverService = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(
    \Pixelant\PxaSiteimprove\Service\PageUrlEidService::class
);

echo $pagePathResolverService->getLink();
