<?php

namespace App\Controller;
use App\Entity\User;
use App\Forms\RegistrationForm;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class AuthorizationController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/registration', name: 'registration')]
    public function registrationAction(Request $request, UserPasswordHasherInterface $passwordHasher): Response
    {

        $user = new User();
        $authorizationForm = $this->createForm(RegistrationForm::class, $user);

        $authorizationForm->handleRequest($request);

        if ($authorizationForm->isSubmitted() && $authorizationForm->isValid()) {

            $user = $authorizationForm->getData();
            $plaintextPassword = $user->getPassword();

            $hashedPassword = $passwordHasher->hashPassword(
                $user,
                $plaintextPassword
            );
            $user->setPassword($hashedPassword);
            $this->entityManager->persist($user);
            //dump($user);
            $this->entityManager->flush();

            return $this->redirectToRoute('app_login');
        }

        return $this->render('auth/registration.html.twig', [
            'authorizationForm' => $authorizationForm
        ]);
    }

    #[\Symfony\Component\Routing\Attribute\Route('/login', name: 'app_login')]
    public function index(AuthenticationUtils $authenticationUtils): Response
    {
        $error = $authenticationUtils->getLastAuthenticationError();

        // последнее имя пользователя, введенное пользователем
        $lastUsername = $authenticationUtils->getLastUsername();

        if($this->getUser() != null) // если пользователь зарегестрирован
        {
            return $this->redirectToRoute('news'); // перемещаем его на главную страницу
        }

        return $this->render('login/index.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error
        ]);
    }
}