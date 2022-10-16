<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Form\ProfileFormType;
use App\Form\PasswordResetFormType;
use App\Security\EmailVerifier;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mime\Address;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;

use Psr\Log\LoggerInterface;

class RegistrationController extends AbstractController
{
    private EmailVerifier $emailVerifier;
    private LoggerInterface $logger;

    public function __construct(EmailVerifier $emailVerifier, LoggerInterface $logger)
    {
        $this->emailVerifier = $emailVerifier;
        $this->logger = $logger;
    }
    /*
    public function index(UserPasswordHasherInterface $passwordHasher)
    {


        // ... e.g. get the user data from a registration form
        $user = new User();
        $plaintextPassword = "";

        // hash the password (based on the security.yaml config for the $user class)
        $hashedPassword = $passwordHasher->hashPassword(
            $user,
            $plaintextPassword
        );
        $user->setPassword($hashedPassword);


        return $this->render('registration/register.html.twig', [
            'controller_name' => 'RegistrationController',
            'last_username' => "",
            'error' => "",
        ]);
    }
    */


    #[Route('/registration', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response
    {

        //Restrict to unauthenticated users only - no need to register once signed in
        if ($this->getUser()){
            return $this->redirectToRoute('index');
        }

        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);
        $userData = null;

        if ($form->isSubmitted()) {

            try {

                $firstName = $form->get('firstName')->getData();
                $lastName = $form->get('lastName')->getData();
                $addressCity = $form->get('addressCity')->getData();
                $addressState = $form->get('addressState')->getData();
                $addressZipCode = $form->get('addressZipCode')->getData();
                $dateOfBirth = $form->get('dateOfBirth')->getData();
                $phoneNumber = $form->get('phoneNumber')->getData();
                $terms = $form->get('agreeTerms')->getData();

                $this->logger->info("firstName: " . gettype($firstName) . ";" . $firstName);
                $this->logger->info("lastName: " . gettype($lastName) . ";" . $lastName);
                $this->logger->info("terms: " . gettype($terms) . ";" . $terms);
                $this->logger->info("addressCity: " . gettype($addressCity) . ";" . $addressCity);
                $this->logger->info("addressState: " . gettype($addressState) . ";" . $addressState);
                $this->logger->info("addressZipCode: " . gettype($addressZipCode) . ";" . $addressZipCode);
                $this->logger->info("phoneNumber: " . gettype($phoneNumber) . ";" . $phoneNumber);
                $this->logger->info("dateOfBirth: " . gettype($dateOfBirth));

                if (!$terms){
                    throw new \Exception("Please agree to the terms and conditions to proceed.");
                }

                $user->setIsVerified(true);
                $user->setFirstName($firstName);
                $user->setLastName($lastName);
                $user->setAddressCity($addressCity);
                $user->setAddressState($addressState);
                $user->setAddressZipCode($addressZipCode);
                $user->setPhoneNumber($phoneNumber);
                $user->setDateOfBirth($dateOfBirth);

                // encode the plain password
                $user->setPassword(
                    $userPasswordHasher->hashPassword(
                        $user,
                        $form->get('plainPassword')->getData()
                    )
                );

                $user->setRegistrationDate((new \DateTime()));
                $user->setRoles(["ROLE_USER"]);

                if (!$form->isValid()){

                    /* ## If you wanted to manually check every form field...
                    $formErrors = [];

                    foreach ($form->all() as $childForm) {
                        if ($childErrors = $childForm->getErrors()) {
                            foreach ($childErrors as $error) {
                                $formErrors[$error->getOrigin()->getName()] = $error->getMessage();
                            }
                        }
                    }
                    */

                    $userData = new \stdClass();
                    $userData->firstName = $user->getFirstName();
                    $userData->lastName = $user->getLastName();
                    $userData->email = $user->getEmail();
                    $userData->addressCity = $user->getAddressCity();
                    $userData->addressState = $user->getAddressState();
                    $userData->addressZipCode = $user->getAddressZipCode();
                    $userData->phoneNumber = $user->getPhoneNumber();
                    $userData->dateOfBirth = $user->getDateOfBirth();

                    throw new \Exception("Please address the following errors.");
                }

                $entityManager->persist($user);
                $entityManager->flush();
            }
            catch (\Exception $ex) {

                 $this->addFlash('error', $ex->getMessage());
                // Note: $this->addFlash() is equivalent to $request->getSession()->getFlashBag()->add()

                return $this->render('registration/register.html.twig', [
                    'registrationForm' => $form->createView(),
                    'userData' => $userData
                ]);
            }

            // generate a signed url and email it to the user --- Skip for now
            /*
            $this->emailVerifier->sendEmailConfirmation('app_verify_email', $user,
                (new TemplatedEmail())
                    ->from(new Address('erikjohnson06@gmail.com', 'GitHub PHP Project Finder Team'))
                    ->to($user->getEmail())
                    ->subject('Please Confirm your Email')
                    ->htmlTemplate('registration/confirmation_email.html.twig')
            );
            */

            $this->addFlash('success', "Registration successful! Please login with your username and password.");

            return $this->redirectToRoute('app_login');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
            'userData' => $userData
        ]);
    }

    #[Route('/editProfile', name: 'app_edit_profile')]
    public function editProfile(Request $request, EntityManagerInterface $entityManager): Response
    {
        //Limit area to authenticated users only
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $user = $this->getUser();
        $form = $this->createForm(ProfileFormType::class, $user);
        $form->handleRequest($request);
        $userData = null;

        if ($form->isSubmitted()) {

            try {

                $firstName = $form->get('firstName')->getData();
                $lastName = $form->get('lastName')->getData();
                $email = $form->get('email')->getData();
                $addressCity = $form->get('addressCity')->getData();
                $addressState = $form->get('addressState')->getData();
                $addressZipCode = $form->get('addressZipCode')->getData();
                $dateOfBirth = $form->get('dateOfBirth')->getData();
                $phoneNumber = $form->get('phoneNumber')->getData();

                $user->setFirstName($firstName);
                $user->setLastName($lastName);
                $user->setAddressCity($addressCity);
                $user->setAddressState($addressState);
                $user->setAddressZipCode($addressZipCode);
                $user->setPhoneNumber($phoneNumber);
                $user->setDateOfBirth($dateOfBirth);
                $user->setEmail($email);

                if (!$form->isValid()){

                    /* # Manually check every form field
                    $formErrors = [];

                    foreach ($form->all() as $childForm) {
                        if ($childErrors = $childForm->getErrors()) {
                            foreach ($childErrors as $error) {
                                $formErrors[$error->getOrigin()->getName()] = $error->getMessage();
                            }
                        }
                    }
                    */

                    $userData = new \stdClass();
                    $userData->firstName = $user->getFirstName();
                    $userData->lastName = $user->getLastName();
                    $userData->email = $user->getEmail();
                    $userData->addressCity = $user->getAddressCity();
                    $userData->addressState = $user->getAddressState();
                    $userData->addressZipCode = $user->getAddressZipCode();
                    $userData->phoneNumber = $user->getPhoneNumber();
                    $userData->dateOfBirth = $user->getDateOfBirth();

                    throw new \Exception("Please address the following errors:");
                }

                //Save updates, but make sure the email is still unique
                $entityManager->persist($user);
                $entityManager->flush();
            }
            catch (\Exception $ex) {

                 $this->addFlash('error', $ex->getMessage());

                return $this->render('profile/edit.html.twig', [
                    'profileForm' => $form->createView(),
                    'userData' => $userData
                ]);
            }

            //If successful, redirect back to main page
            $this->addFlash('success', "Profile update successful!");

            return $this->redirectToRoute('index');
        }

        return $this->render('profile/edit.html.twig', [
            'profileForm' => $form->createView(),
            'userData' => $userData
        ]);
    }

    #[Route('/updatePassword', name: 'app_update_password')]
    public function updatePassword(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response
    {
        //Limit area to authenticated users only
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $user = $this->getUser();
        $form = $this->createForm(PasswordResetFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {

            try {

                if (!$form->isValid()){
                    throw new \Exception("Please address the following errors.");
                }

                //Probably a good idea to force the user to enter their current password first...
                $newPassword1 = $form->get('plainPasswordNew1')->getData();
                $newPassword2 = $form->get('plainPasswordNew2')->getData();

                if ($newPassword1 !== $newPassword2){
                    throw new \Exception("New passwords do not match.");
                }

                $user->setPassword(
                    $userPasswordHasher->hashPassword(
                        $user,
                        $newPassword1
                    )
                );

                $entityManager->persist($user);
                $entityManager->flush();
            }
            catch (\Exception $ex) {

                $this->addFlash('error', $ex->getMessage());

                return $this->render('profile/update_password.html.twig', [
                    'profileForm' => $form->createView()
                ]);
            }

            //If successful, redirect back to main page
            $this->addFlash('success', "Password updated successfully!");

            return $this->redirectToRoute('index');
        }

        return $this->render('profile/update_password.html.twig', [
            'profileForm' => $form->createView()
        ]);
    }

    #[Route('/verify/email', name: 'app_verify_email')]
    public function verifyUserEmail(Request $request): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        // validate email confirmation link, sets User::isVerified=true and persists
        try {
            $this->emailVerifier->handleEmailConfirmation($request, $this->getUser());
        } catch (VerifyEmailExceptionInterface $exception) {
            $this->addFlash('verify_email_error', $exception->getReason());

            return $this->redirectToRoute('app_register');
        }

        $this->addFlash('success', 'Your email address has been verified.');

        return $this->redirectToRoute('app_register');
    }
}
