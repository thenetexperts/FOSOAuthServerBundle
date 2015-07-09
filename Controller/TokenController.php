<?php

/*
 * This file is part of the FOSOAuthServerBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FOS\OAuthServerBundle\Controller;

use FOS\OAuthServerBundle\Event\OAuthLoginEvent;
use OAuth2\Model\IOAuth2AccessToken;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use OAuth2\OAuth2;
use OAuth2\OAuth2ServerException;
use Symfony\Component\HttpFoundation\Response;
use Tne\ApiBundle\Entity\User;

class TokenController
{
    /**
     * @var OAuth2
     */
    protected $server;

    /**
     * @var EventDispatcherInterface
     */
    protected $dispatcher;

    /**
     * @param OAuth2 $server
     * @param EventDispatcherInterface $dispatcher
     */
    public function __construct(OAuth2 $server, EventDispatcherInterface $dispatcher)
    {
        $this->server = $server;
        $this->dispatcher = $dispatcher;
    }

    /**
     * @param  Request $request
     * @return type
     */
    public function tokenAction(Request $request)
    {
        try {
            $response = $this->server->grantAccessToken($request);

            $jsonResponse = json_decode($response->getContent());
            $accessToken = $jsonResponse->access_token;

            if ($accessToken != '' && $response instanceof Response && $response->getStatusCode() == 200) {
                // get token object
                $token = $this->server->verifyAccessToken($accessToken);

                // dispatch login event
                if ($token instanceof IOAuth2AccessToken && $token->getUser() instanceof User) {
                    $loginEvent = new OAuthLoginEvent($token->getUser(), $request);
                    $this->dispatcher->dispatch(OAuthLoginEvent::LOGIN, $loginEvent);
                }
            }

            return $response;
        } catch (OAuth2ServerException $e) {
            return $e->getHttpResponse();
        }
    }
}
