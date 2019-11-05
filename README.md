# pxa_siteimprove
[![Build Status](https://travis-ci.org/pixelant/pxa_siteimprove.svg?branch=master)](https://travis-ci.org/pixelant/pxa_siteimprove)

An integration of Siteimprove (https://siteimprove.com/) into TYPO3 CMS. A modal
will be available in the Page Module for site and page analysis.

## Installation

Install the extension through composer for best process

    composer require pixelant/pxa-siteimprove

## Configuration

The following commands are available in the Extension Manager settings.

    debugMode - For getting debug information regarding the API connection

    token - The API token to use for Siteimprove. If none is provided the default kicks in

## Deep Linking

A deep link to a page is of the following form:

    https://example.com/typo3/index.php?tx_siteimprove_goto=page:{page_uid}:{language_uid}
    
Whereas the `language_uid` is optional and defaults to `0`. Example links could look like this:

    https://example.com/typo3/index.php?tx_siteimprove_goto=page:42
    https://example.com/typo3/index.php?tx_siteimprove_goto=page:42:1

## Documentation

For all kind of documentation which covers install to how to develop the extension:

[Local Documentation](Documentation/Index.rst)

[Online Documentation](https://docs.typo3.org/typo3cms/extensions/pxa_siteimprove/)