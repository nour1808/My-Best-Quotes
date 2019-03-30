<?php

namespace App\Form;

use App\Entity\User;
use App\Form\ApplicationType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Vich\UploaderBundle\Form\Type\VichImageType;

class RegistrationType extends ApplicationType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstName', TextType::class, $this->getConfiguration('Firstname', 'Enter your first name'))
            ->add('lastName', TextType::class, $this->getConfiguration('Lastname', 'Enter your last name'))
            ->add('email', EmailType::class, $this->getConfiguration('Email', 'Enter your email'))
            ->add('gender', ChoiceType::class, $this->getConfiguration('Gender', 'Enter your gender', ['choices'  => ['Male' => 'male', 'Female' => 'female']]))
            ->add('pictureFile', VichImageType::class, $this->getConfiguration('Profile picture', 'Choose your profile picture', ['required' => false]))
            ->add('hash', PasswordType::class, $this->getConfiguration('Password', 'Enter your password'))
            ->add('passwordConfirm', PasswordType::class, $this->getConfiguration('Confirmation of password', 'Reenter your password'));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}