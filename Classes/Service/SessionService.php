<?php

namespace BZgA\BzgaBeratungsstellensuche\Service;

use TYPO3\CMS\Frontend\Authentication\FrontendUserAuthentication;

class SessionService
{
    /**
     * @var string
     */
    const SESSIONNAMESPACE = 'beratungsstellendatenbank_session';

    /**
     * @return mixed
     */
    public function restoreFromSession()
    {
        if ($this->hasValidFrontendUser()) {
            $sessionData = $this->getFrontendUser()->getKey('ses', self::SESSIONNAMESPACE);

            return unserialize($sessionData);
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
            $this->getFrontendUser()->setKey('ses', self::SESSIONNAMESPACE, $sessionData);
        }
    }

    /**
     * @return SessionService
     */
    public function cleanUpSession()
    {
        if ($this->hasValidFrontendUser()) {
            $this->getFrontendUser()->setKey('ses', self::SESSIONNAMESPACE, null);
        }
    }

    /**
     * @return bool
     */
    protected function hasValidFrontendUser()
    {
        if ($this->getFrontendUser() instanceof FrontendUserAuthentication) {
            return true;
        }

        return false;
    }

    /**
     * @return FrontendUserAuthentication
     */
    protected function getFrontendUser()
    {
        if ($GLOBALS ['TSFE']->fe_user) {
            return $GLOBALS ['TSFE']->fe_user;
        }
    }
}
