<?php
namespace Acilia\Bundle\OauthAuthorizationBundle\Security;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class TokenAuthenticator extends AbstractGuardAuthenticator
{
    protected $access_url;
    protected $enabled;
    protected $client_id;

    public function __construct($access_url, $enabled, $client_id)
    {
        $this->access_url = $access_url;
        $this->enabled = $enabled;
        $this->client_id = $client_id;
    }

    /**
     * Called on every request. Return whatever credentials you want,
     * or null to stop authentication.
     */
    public function getCredentials(Request $request)
    {
        if ($this->enabled == false) {
            return true;
        }

        if (!$token = $request->query->get('access_token', false)) {
            // no token? Return null and no other methods will be called
            return;
        }

        return ['token' => $token];
        // What you return here will be passed to getUser() as $credentials
    }

    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        $accessToken = $credentials['token'];

        return $userProvider->loadUserByUsername($accessToken);
    }

    public function checkCredentials($credentials, UserInterface $user)
    {
        //return in_array($this->regionService->getCode(), $user->getRegions());
        return true;
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        // on success, let the request continue

        //$route = explode('?', $request->getRequestUri(), 2)[0];
        //return new RedirectResponse($route);
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        $redirectUri = urlencode($request->getSchemeAndHttpHost().$request->getBaseUrl().$request->getPathInfo());
        $redirectUri = sprintf('%s/oauth/auth?client_id=%s&redirect_uri=%s', $this->access_url, $this->client_id, $redirectUri);
        return new RedirectResponse($redirectUri);
    }

    /**
     * Called when authentication is needed, but it's not sent
     */
    public function start(Request $request, AuthenticationException $authException = null)
    {
        if ($this->enabled == false) {
            return true;
        }

        $redirectUri = urlencode($request->getSchemeAndHttpHost().$request->getBaseUrl().$request->getPathInfo());
        $redirectUri = sprintf('%s/oauth/auth?client_id=%s&redirect_uri=%s', $this->access_url, $this->client_id, $redirectUri);
        return new RedirectResponse($redirectUri);
    }

    public function supportsRememberMe()
    {
        return false;
    }
}