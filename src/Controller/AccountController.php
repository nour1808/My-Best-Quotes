<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\AccountType;
use App\Entity\ResetPassword;
use App\Entity\PasswordUpdate;
use App\Form\RegistrationType;
use App\Form\PasswordResetType;
use App\Form\ResetPasswordType;
use App\Form\PasswordUpdateType;
use App\Repository\UserRepository;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\PasswordUpdate as AppPasswordUpdate;
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
                "Your account has been created, you can now sign in."
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
                "Your profile has been modified"
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
                $form->get('oldPassword')->addError(new FormError("The password you typed is not your current password."));
            } else {

                $newPassword = $passwordUpdate->getNewPassword();
                $hash = $encoder->encodePassword($user, $newPassword);
                $user->setHash($hash);
                $manager->persist($user);
                $manager->flush();

                $this->addFlash(
                    'success',
                    "Your profile has been modified"
                );
                return $this->redirectToRoute('account_profile');
            }
        }

        return $this->render('account/password.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/account/reset-password", name="account_reset")
     */
    public function resetPassword(UserRepository $repo, Request $request, ObjectManager $manager, UserPasswordEncoderInterface $encoder, \Swift_Mailer $mailer)
    {
        $resetPssword = new ResetPassword();
        $form = $this->createForm(ResetPasswordType::class, $resetPssword);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $email =  $resetPssword->getEmail();
            $user = $repo->findOneByEmail($email);
            if ($user) {
                $token = uniqid();
                $user->setResetPassword($token);
                $manager->persist($user);
                $manager->flush();

                $message = (new \Swift_Message('Reset Password For your account'))
                    ->setFrom('contact@berosta.com')
                    ->setTo($user->getEmail())
                    ->setBody(
                        $this->renderView(
                            // templates/emails/registration.html.twig
                            'emails/reset-password.html.twig',
                            ['user' => $user]
                        ),
                        'text/html'
                    );

                if ($mailer->send($message)) {
                    $this->addFlash(
                        'success',
                        "We have sent an email that contains the reset of your password."
                    );
                }
            } else {
                $form->get('email')->addError(new FormError("No user exists in our database with this email, thank you to resubscribe with another valid email address. "));
            }
        }

        return $this->render('account/reset-password.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/account/reset-password/{token}", name="account_reset_token")
     */
    public function resetPasswordToken(UserRepository $repo, $token, Request $request, ObjectManager $manager, UserPasswordEncoderInterface $encoder)
    {

        $user = $repo->findOneByResetPassword($token);
 
        if ($token && $user) {
            $passwordUpdate = new PasswordUpdate();
            $form = $this->createForm(PasswordResetType::class, $passwordUpdate);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $newPassword = $passwordUpdate->getNewPassword();
                $hash = $encoder->encodePassword($user, $newPassword);
                $user->setHash($hash);
                $user->setResetPassword(null);
                $manager->persist($user);
                $manager->flush();
                $this->addFlash(
                    'success',
                    "Your password has been modified"
                );
                return $this->redirectToRoute('account_login');
            }

            return $this->render('account/reset-password-token.html.twig', [
                'form' => $form->createView(),
            ]);
        } else {
            $this->addFlash(
                'danger',
                "invalid Token "
            );
        }
        return $this->redirectToRoute('account_reset');
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
