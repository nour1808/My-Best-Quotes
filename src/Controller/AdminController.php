<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\PasswordUpdate;
use FOS\UserBundle\Doctrine\UserManager;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use EasyCorp\Bundle\EasyAdminBundle\Controller\EasyAdminController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AdminController extends EasyAdminController
{

    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    private function updateHash($entity)
    {

        if (method_exists($entity, 'setHash') and method_exists($entity, 'getHash')) {
            if ($entity->getHash() != "" && isset($_POST['user']['hash']) && $_POST['user']['hash'] != "") {
                $newPassword = $entity->getHash();
                $hash = $this->encoder->encodePassword($entity, $newPassword);
                $entity->setHash($hash);
            } else {
                $userQ = $this->getDoctrine()
                    ->getRepository(User::class)
                    ->findOneBy(['id' => $entity->getId()]);
                $entity->setHash($userQ->getHash());
            }
        }
    }

    private function updateRole($entity)
    {

        if ($entity->getHash() != "" && $_POST['user']['hash'] != "") {
            $newPassword = $entity->getHash();
            $hash = $this->encoder->encodePassword($entity, $newPassword);
            $entity->setHash($hash);
        } else {
            $userQ = $this->getDoctrine()
                ->getRepository(User::class)
                ->findOneBy(['id' => $entity->getId()]);
            $entity->setHash($userQ->getHash());
        }
    }

    public function updateUserEntity($entity)
    {
        //var_dump($_POST['user']);die;
        $this->updateHash($entity);
        //$this->updateRole($entity);
        parent::updateEntity($entity);
    }

    public function persistEntity($entity)
    {
        $this->updateHash($entity);
        parent::persistEntity($entity);
    }

    /*
    public function createNewUserEntity(){
        
        if(isset($_POST['user']['hash'])){
            $user = new User();
            $hash = $this->encoder->encodePassword($user, $_POST['user']['hash']);
            $user->setHash($hash);
            die($user->getFirstName());
            return $user;
        }
    }
*/
}