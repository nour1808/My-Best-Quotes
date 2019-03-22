<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\AccountType;
use App\Entity\PasswordUpdate;
use App\Form\RegistrationType;
use App\Form\PasswordUpdateType;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AccountController extends AbstractController
{
    /**
     * @Route("/login", name="account_login")
     */
    public function login(AuthenticationUtils $utils)
    {
        $error = $utils->getLastAuthenticationError();
        $username = $utils->getLastUsername();
        return $this->render('account/login.html.twig', [
            'hasError' => $error != null,
            'username' => $username,
        ]);
    }

    /**
     * @Route("/logout", name="account_logout")
     */
    public function logout()
    { }

    /**
     * @Route("/account/register", name="account_register")
     */
    public function register(Request $request, ObjectManager $manager, UserPasswordEncoderInterface $encoder)
    {
        $user = new User();
        $form = $this->createForm(RegistrationType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $hash = $encoder->encodePassword($user, $user->getHash());
            $user->setHash($hash);
            $manager->persist($user);
            $manager->flush();

            $this->addFlash(
                'success',
                "Votre compte a bien été créé, vous pouvez maitenant vous connecter."
            );

            return $this->redirectToRoute('account_login');
        }

        return $this->render('account/registration.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/account/profile", name="account_profile")
     * @IsGranted("ROLE_USER")
     */
    public function profile(Request $request, ObjectManager $manager)
    {
        $user = $this->getUser();
        $form = $this->createForm(AccountType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //$hash = $encoder->encodePassword($user, $user->getHash());
            //$user->setHash($hash);
            $manager->persist($user);
            $manager->flush();

            $this->addFlash(
                'success',
                "Votre profile a bien été modifié"
            );

            return $this->redirectToRoute('account_profile');
        }

        return $this->render('account/profile.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/account/password-update", name="account_password")
     * @IsGranted("ROLE_USER")
     */
    public function updatePassword(Request $request, ObjectManager $manager, UserPasswordEncoderInterface $encoder)
    {
        $passwordUpdate = new PasswordUpdate();
        $form = $this->createForm(PasswordUpdateType::class, $passwordUpdate);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //$hash = $encoder->encodePassword($user, $user->getHash());
            //$user->setHash($hash);
            $user = $this->getUser();

            // 1. Verifier si le oldpassword du form est identitque que celui de user
            if (!password_verify($passwordUpdate->getOldPassword(), $user->getHash())) {
                $form->get('oldPassword')->addError(new FormError("Le mot de passe que vous avez tappé n'est pas votre mot de passe actuel."));
            } else {

                $newPassword = $passwordUpdate->getNewPassword();
                $hash = $encoder->encodePassword($user, $newPassword);
                $user->setHash($hash);
                $manager->persist($user);
                $manager->flush();

                $this->addFlash(
                    'success',
                    "Votre profile a bien été modifié"
                );
                return $this->redirectToRoute('account_profile');
            }
        }

        return $this->render('account/password.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/account", name="account_index")
     * @IsGranted("ROLE_USER")
     */
    public function myAccount()
    {
        return $this->render('user/index.html.twig', [
            'user' => $this->getUser()
        ]);
    }
}