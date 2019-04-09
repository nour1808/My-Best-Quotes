<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Quote;
use App\Form\AccountType;
use App\Entity\ResetPassword;
use App\Entity\PasswordUpdate;
use App\Form\RegistrationType;
use App\Form\PasswordResetType;
use App\Form\ResetPasswordType;
use App\Form\PasswordUpdateType;
use App\Repository\UserRepository;
use Symfony\Component\Form\FormError;
use Knp\Component\Pager\PaginatorInterface;
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
    public function register(Request $request, ObjectManager $manager, UserPasswordEncoderInterface $encoder, \Swift_Mailer $mailer)
    {
        $user = new User();
        $form = $this->createForm(RegistrationType::class, $user);
        $defaultPicture = "default-user-image.png";

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            if (!$user->getPicture()) {
                $user->setPicture($defaultPicture);
            }

            $hash = $encoder->encodePassword($user, $user->getHash());
            $user->setHash($hash);

            $token = sha1(uniqid());
            $user->setResetPassword($token);

            $manager->persist($user);
            $manager->flush();

            $subject = "Activate your account myquotes.be";
            $templateName = "registration.html.twig";

            $this->MailTO(null, $user->getEmail(), $subject, $templateName, $user, 'user', $mailer);

            $this->addFlash(
                'success',
                "Your account has been created, an email has been sent to your mailbox thank you to click on the activation link."
            );

            return $this->redirectToRoute('account_login');
        }

        return $this->render('account/registration.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/account/auth/activate/{token}", name="account_register_token")
     */
    public function ActivateAccount(UserRepository $repo, $token, ObjectManager $manager)
    {
        $user = $repo->findOneByResetPassword($token);
        if ($token && $user) {
            $user->setResetPassword(null);
            $user->setIsActif(true);
            $manager->persist($user);
            $manager->flush();
            $this->addFlash(
                'success',
                "Your account has been successfully activated."
            );
        } else {
            $this->addFlash(
                'danger',
                "Invalid Token."
            );
        }

        return $this->redirectToRoute('account_login');
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
     * @Route("/quotes/{slug}", name="account_profile_slug")
     */
    public function showProfile(User $user)
    {
        if (!$user) {
            throw $this->createNotFoundException('This user has been deactivated');
        }

        return $this->render('user/profile-slug.html.twig', [
            'user' => $user,
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
                $token = sha1(uniqid());
                $user->setResetPassword($token);
                $manager->persist($user);
                $manager->flush();

                $subject = "Reset Password For your account";
                $templateName = "reset-password.html.twig";

                $this->MailTO(null, $user->getEmail(), $subject, $templateName, $user, 'user', $mailer);

                $this->addFlash(
                    'success',
                    "We have sent an email that contains the reset of your password."
                );
            } else {
                $form->get('email')->addError(new FormError("No user exists in our database with this email, thank you to resubscribe with another valid email address. "));
            }
        }

        return $this->render('account/reset-password.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    protected function MailTO($emailFrom, $emailTo, $subject, $templateName, $entity, $labelEntity, $mailer)
    {

        if (!$emailFrom) {
            $emailFrom = 'contact@berosta.com';
        }

        if (!$emailTo) {
            $emailFrom = $entity->getEmail();
        }

        $message = (new \Swift_Message($subject))
            ->setFrom($emailFrom)
            ->setTo($emailTo)
            ->setBody(
                $this->renderView(
                    "emails/$templateName",
                    [$labelEntity => $entity]
                ),
                'text/html'
            );

        $mailer->send($message);
        //return $message;

        /* $message = (new \Swift_Message('Reset Password For your account'))
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
        */
    }

    /**
     * @Route("/account/auth/reset-password/{token}", name="account_reset_token")
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
    public function myAccount(Request $request, PaginatorInterface $paginator)
    {
        $pagination = $paginator->paginate(
            $this->getUser()->getQuotes(), /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            10 /*limit per page*/
        );

        return $this->render('user/index.html.twig', [
            'user' => $this->getUser(),
            'pagination' => $pagination
        ]);
    }
}
