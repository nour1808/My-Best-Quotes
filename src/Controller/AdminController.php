<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\PasswordUpdate;
use FOS\UserBundle\Doctrine\UserManager;
use Symfony\Component\DependencyInjection\ContainerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Controller\EasyAdminController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AdminController extends EasyAdminController
{

    /**
     * @var UserPasswordEncoderInterface
     */
    private $passwordEncoder;

    /**
     * UserController constructor.
     *
     * @param UserPasswordEncoderInterface $passwordEncoder
     */
    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    public function persistEntity($entity)
    {
        $this->encodePassword($entity);
        parent::persistEntity($entity);
    }

    public function updateEntity($entity)
    {
        $this->encodePassword($entity);
        parent::updateEntity($entity);
    }

    public function encodePassword($user)
    {
        if (!$user instanceof User) {
            return;
        }

        $user->setHash(
            $this->passwordEncoder->encodePassword($user, $user->getHash())
        );
    }

    /*
    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }


    public function createNewUserEntity()
    {

        $user = $this->entity();

        var_dump($user);
        die;
        if ($user->getHash()) {
            $newPassword = $passwordUpdate->getNewPassword();
            $hash = $this->encoder->encodePassword($user, $newPassword);
            $user->setHash($hash);
        }
        parent::persistEntity($user);
    }

    public function persistUserEntity(User $user)
    {
        var_dump("Persist :");
        var_dump($user);
        die;

        parent::persistEntity($user);
    }

    public function updateUserEntity(User $user)
    {
        var_dump("Update :");
        if (!$user->getHash()) {
            return;
        }
        $encodedPassword = $this->encodePassword($user, $user->getHash());
        $user->setPassword($encodedPassword);

        parent::updateEntity($user);
    }

    private function encodePassword(User $user, $password, UserPasswordEncoderInterface $encoder)
    {
        return $encoder->encodePassword($user, $password);
    }

    
    protected function prePersistUserEntity(User $user)
    {
        die('t1');
        $encodedPassword = $this->encodePassword($user, $user->getPassword());
        $user->setPassword($encodedPassword);
    }

    protected function preUpdateUserEntity(User $user)
    {
        die('t2');
        if (!$user->getPlainPassword()) {
            return;
        }
        $encodedPassword = $this->encodePassword($user, $user->getPlainPassword());
        $user->setPassword($encodedPassword);
    }

    private function encodePassword($user, $password)
    {
        $passwordEncoderFactory = $this->get('security.encoder_factory');
        $encoder = $passwordEncoderFactory->getEncoder($user);
        return $encoder->encodePassword($password, $user->getSalt());
    }
    */
}