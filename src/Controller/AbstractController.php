<?php

namespace App\Controller;

use App\Entity\User;

abstract class AbstractController extends \Symfony\Bundle\FrameworkBundle\Controller\AbstractController
{
    /**
     * Get a user from the Security Token Storage.
     *
     * @return User|null
     *
     * @throws \LogicException If SecurityBundle is not available
     *
     * @see TokenInterface::getUser()
     */
    protected function getUser()
    {
        if (!$this->container->has('security.token_storage')) {
            throw new \LogicException('The SecurityBundle is not registered in your application. Try running "composer require symfony/security-bundle".');
        }

        if (null === $token = $this->container->get('security.token_storage')->getToken()) {
            return null;
        }

        // @deprecated since 5.4, $user will always be a UserInterface instance
        if (!\is_object($user = $token->getUser())) {
            // e.g. anonymous authentication
            return null;
        }

        if (!$user instanceof User) {
            throw new \Exception('User is not instance of User:class');
        }

        return $user;
    }
}