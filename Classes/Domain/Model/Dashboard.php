<?php
namespace Pixelant\PxaSiteimprove\Domain\Model;

/***
 *
 * This file is part of the "Siteimprove" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 *  (c) 2018 Mattias Nilsson <mattias@pixelant.se>, Pixelant AB
 *
 ***/

/**
 * Dashboard
 */
class Dashboard extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity
{
    /**
     * page
     *
     * @var string
     */
    protected $page = '';

    /**
     * token
     *
     * @var string
     */
    protected $token = '';

    /**
     * Returns the page
     *
     * @return string $page
     */
    public function getPage()
    {
        return $this->page;
    }

    /**
     * Sets the page
     *
     * @param string $page
     * @return void
     */
    public function setPage($page)
    {
        $this->page = $page;
    }

    /**
     * Returns the token
     *
     * @return string $token
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * Sets the token
     *
     * @param string $token
     * @return void
     */
    public function setToken($token)
    {
        $this->token = $token;
    }
}
