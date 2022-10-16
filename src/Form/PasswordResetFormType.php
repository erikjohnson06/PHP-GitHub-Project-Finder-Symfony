<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

class PasswordResetFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('plainPasswordNew1', PasswordType::class, [
                'required' => true,
                'mapped' => false,
                'attr' => [
                    'autocomplete' => 'new-password',
                    'placeholder' => '6-20 Characters'
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter a new password',
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Your password should be at least {{ limit }} characters',
                        'max' => 20,
                        'maxMessage' => 'Please limit your password to {{ limit }} characters',
                    ]),
                    new Regex([
                        "pattern" => "/^(?=[-_a-zA-Z0-9]*?[A-Za-z])(?=[-_a-zA-Z0-9]*?[0-9])[!.-_a-zA-Z0-9]{6,20}$/",
                        "message" => "Invalid password. New passwords must consist of 6 to 20 characters and at least one numeric character. Special characters may include hyphens, underscores, exclamation points, and periods."
                    ])
                ],
            ])
            ->add('plainPasswordNew2', PasswordType::class, [
                'required' => true,
                'mapped' => false,
                'attr' => [
                    'autocomplete' => 'new-password',
                    'placeholder' => '6-20 Characters'
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please re-enter your new password',
                    ])
                ],
            ])
            //->setAction('app_edit_profile')
            ->setMethod('POST')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'csrf_protection' => true,
            'csrf_field_name' => '_token', // the name of the hidden HTML field that stores the token
            'csrf_token_id'   => 'user',
        ]);
    }
}
