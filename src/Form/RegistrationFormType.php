<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstName', TextType::class, [
                'required' => true,
                'label' => 'First Name',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter your first name.',
                    ]),
                    new Length([
                        'min' => 1,
                        'minMessage' => 'Please enter your first name',
                        'max' => 100,
                        'maxMessage' => 'Please limit your first name to {{ limit }} characters',
                    ]),
                    new Regex([
                        "pattern" => "/^[A-Za-z0-9\-\_\#\.\/\s\!\;\&]+$/",
                        "message" => "First name contains invalid characters."
                    ])
                ]
            ])
            ->add('lastName', TextType::class, [
                'required' => true,
                'label' => 'Last Name',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter your last name.',
                    ]),
                    new Length([
                        'min' => 1,
                        'minMessage' => 'Please enter your last name',
                        'max' => 100,
                        'maxMessage' => 'Please limit your last name to {{ limit }} characters',
                    ]),
                    new Regex([
                        "pattern" => "/^[A-Za-z0-9\-\_\#\.\/\s\!\;\&]+$/",
                        "message" => "Last name contains invalid characters."
                    ])
                ]
            ])
            ->add('email', EmailType::class, [
                'attr' => [
                    'placeholder' => 'Ex: your@email.com'
                ],
                'required' => true,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter an email address.',
                    ]),
                    new Length([
                        'max' => 100,
                        'maxMessage' => 'Please limit your email address to {{ limit }} characters',
                    ]),
                    new Regex([
                        "pattern" => "/^[\w\-\_\.]+@([\w\-\_]+\.)+[\w\-\_]{2,4}$/",
                        "message" => "Email appears to be invalid."
                    ])
                ],
            ])
            ->add('plainPassword', PasswordType::class, [
                // instead of being set onto the object directly,
                // this is read and encoded in the controller
                'required' => true,
                'mapped' => false,
                'attr' => [
                    'autocomplete' => 'new-password',
                    'placeholder' => '6-20 Characters'
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter a password',
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
            ->add('addressCity', TextType::class, [
                'required' => true,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter a city',
                    ]),
                    new Length([
                        'min' => 1,
                        'minMessage' => 'Please enter a valid city',
                        'max' => 100,
                        'maxMessage' => 'Please limit your city to {{ limit }} characters',
                    ]),
                    new Regex([
                        "pattern" => "/^[A-Za-z0-9\-\_\#\.\/\s\!\;\&]+$/",
                        "message" => "City contains invalid characters."
                    ])
                ]
            ])
            ->add('addressState', ChoiceType::class, [
                'choices' => [
                    'TN' => 'TN',
                    'AL' => 'AL',
                    'GA' => 'GA'
                ]
            ])
            ->add('addressZipCode', NumberType::class, [
                'attr' => [
                    'placeholder' => 'Ex: 12345'
                ],
                'required' => true,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter a valid zip code',
                    ]),
                    new Length([
                        'min' => 5,
                        'maxMessage' => 'Please limit your zip code to 5 digits',
                        'max' => 5,
                    ]),
                    new Regex([
                        "pattern" => "/^[0-9]+$/",
                        "message" => "Zip Code contains invalid characters."
                    ])
                ]
            ])
            ->add('phoneNumber', TelType::class, [
                'attr' => [
                    'placeholder' => 'Ex: 111-222-3333'
                ],
                'required' => true,
                'invalid_message' => 'Format invalid.',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter a valid phone number',
                    ]),
                    new Length([
                        'min' => 7,
                        'maxMessage' => 'Please enter a valid phone number',
                        'max' => 20,
                    ]),
                    new Regex([
                        "pattern" => "/^(\d{1}[\s.-]?)?(\d{3}?[\s.-]?)?\d{3}[\s.-]?\d{4}$/",
                        "message" => "Phone Number does not appear to be valid."
                    ])
                ]
            ])
            ->add('dateOfBirth', BirthdayType::class, [
                'required' => true
            ])
            ->add('agreeTerms', CheckboxType::class, [
                'mapped' => false,
                'constraints' => [
                    new IsTrue([
                        'message' => 'Please agree to our terms and conditions.',
                    ]),
                ],
            ])
            //->setAction($this->generateUrl('app_register'))
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
