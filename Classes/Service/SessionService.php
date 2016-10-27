<?php

namespace BZgA\BzgaBeratungsstellensuche\Service;

/**
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

use TYPO3\CMS\Frontend\Authentication\FrontendUserAuthentication;

/**
 * @package TYPO3
 * @subpackage bzga_beratungsstellensuche
 * @author Sebastian Schreiber
 */
class SessionService
{
    /**
     * @var string
     */
    const SESSIONNAMESPACE = 'beratungsstellendatenbank_session';

    /**
     * @var FrontendUserAuthentication
     */

    private $frontendUser;

    /**
     * @var string
     */
    private $sessionNamespace;

    /**
     * SessionService constructor.
     * @param string $sessionNamespace
     */
    public function __construct(
        $sessionNamespace = 'beratungsstellendatenbank'
    ) {
        $this->frontendUser = $GLOBALS['TSFE']->fe_user;
        $this->sessionNamespace = $sessionNamespace;
    }


    /**
     * @return mixed
     */
    public function restoreFromSession()
    {
        if ($this->hasValidFrontendUser()) {
            $sessionData = $this->frontendUser->getKey('ses', $this->sessionNamespace);
            $data = unserialize($sessionData);
            if (is_array($data) && !empty($data)) {
                foreach ($data as $key => $value) {
                    if (empty($value)) {
                        unset($data[$key]);
                    }
                }
            }

            return $data;
        }

        return;
    }

    /**
     * @param $object
     *
     * @return SessionService
     */
    public function writeToSession($object)
    {
        if ($this->hasValidFrontendUser()) {
            $sessionData = serialize($object);
            $this->frontendUser->setKey('ses', $this->sessionNamespace, $sessionData);
        }
    }

    /**
     * @return SessionService
     */
    public function cleanUpSession()
    {
        if ($this->hasValidFrontendUser()) {
            $this->frontendUser->setKey('ses', $this->sessionNamespace, null);
        }
    }

    /**
     * @return bool
     */
    protected function hasValidFrontendUser()
    {
        if ($this->frontendUser instanceof FrontendUserAuthentication) {
            return true;
        }

        return false;
    }
}
