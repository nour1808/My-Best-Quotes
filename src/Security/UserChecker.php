<?php

namespace App\Security;

use App\Exception\AccountDeletedException;
use App\Entity\User as AppUser;
use Symfony\Component\Security\Core\Exception\AccountExpiredException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;



class UserChecker extends AbstractController implements UserCheckerInterface
{
    
    public function checkPreAuth(UserInterface $user)
    {
        if (!$user instanceof AppUser) {
        return;
        }

        if (!$user->getIsActif()) {
            throw $this->createNotFoundException( 'your account has been deactivated, please consult your mailbox to activate your account.'); 
        }
    }

    public function checkPostAuth(UserInterface $user)
    {
        if (!$user instanceof AppUser) {
        return;
        }

        if(!$user->getIsActif()) {
        throw $this->createNotFoundException( 'The page you are looking for no longer exists.');
        }
    }

} 