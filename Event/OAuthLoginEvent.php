<?php

namespace FOS\OAuthServerBundle\Event;

use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\HttpFoundation\Request;
use Tne\ApiBundle\Entity\User;
use Tne\ApiBundle\Util\ApiUtilities;

/**
 * Class OAuthLoginEvent
 *
 * @author Sascha Ahmann <hello@thenetexperts.info>
 * @package FOS\OAuthServerBundle\Event
 */
class OAuthLoginEvent extends Event
{
    const LOGIN =  'fos_oauth_server.login';

    /** @var User */
    private $authenticatedUser;

    /** @var  string */
    private $ipAddress;

    /** @var  string */
    private $userAgent;

    /** @var  string */
    private $clientVersion;

    /**
     * Constructor.
     *
     * @param User $authenticatedUser
     * @param Request $request
     */
    public function __construct(User $authenticatedUser, Request $request = null)
    {
        $this->authenticatedUser = $authenticatedUser;

        if ($request instanceof Request) {
            $this->extractUserClientInfo($request);
        }
    }

    /**
     * @param Request $request
     */
    private function extractUserClientInfo(Request $request)
    {
        $this->ipAddress = ApiUtilities::getClientIp($request);
        $this->userAgent = ApiUtilities::getUserAgent($request);
        $this->clientVersion = ApiUtilities::getClientVersion($request);
    }

    /**
     * Gets the authentication token.
     *
     * @return User
     */
    public function getAuthenticatedUser()
    {
        return $this->authenticatedUser;
    }

    /**
     * @return string
     */
    public function getIpAddress()
    {
        return $this->ipAddress;
    }

    /**
     * @return string
     */
    public function getUserAgent()
    {
        return $this->userAgent;
    }

    /**
     * @return string
     */
    public function getClientVersion()
    {
        return $this->clientVersion;
    }
} 
