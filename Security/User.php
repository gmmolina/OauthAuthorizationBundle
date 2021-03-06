<?php

namespace Acilia\Bundle\OauthAuthorizationBundle\Security;

use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\EquatableInterface;

class User implements UserInterface, EquatableInterface
{
    protected $username;
    protected $regions;
    protected $roles;
    protected $metadata;

    public function __construct($username, array $regions, array $roles, array $metadata)
    {
        $this->username = $username;
        $this->regions = $regions;
        $this->roles = $roles;
        $this->metadata = $metadata;
    }

    public function getRegions()
    {
        return $this->regions;
    }

    public function getRoles()
    {
        return $this->roles;
    }

    public function getPassword()
    {
        return '';
    }

    public function getSalt()
    {
        return null;
    }

    public function getUsername()
    {
        return $this->username;
    }

    public function getMetadata()
    {
        return $this->metadata;
    }

    public function eraseCredentials()
    {
    }

    public function isEqualTo(UserInterface $user)
    {
        if (!$user instanceof self) {
            return false;
        }

        if ($this->username !== $user->getUsername()) {
            return false;
        }

        return true;
    }
}
