<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\LoginType;
use App\Form\RegistrationType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use LogicException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Notifier\Notification\Notification;
use Symfony\Component\Notifier\NotifierInterface;
use Symfony\Component\Notifier\Recipient\Recipient;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;
use Symfony\Component\Security\Http\Authenticator\LoginLinkAuthenticator;
use Symfony\Component\Security\Http\LoginLink\LoginLinkHandlerInterface;
use Symfony\Component\Security\Http\LoginLink\LoginLinkNotification;

class SecurityController extends AbstractController
{
    #[Route('/login', name: 'login', methods: ['GET', 'POST'])]
    public function requestLoginLink(
        LoginLinkHandlerInterface $loginLinkHandler,
        UserRepository $userRepository,
        NotifierInterface $notifier,
        Request $request
    ) {
        $user = new User();

        $loginForm = $this->createForm(LoginType::class, $user);
        $loginForm->handleRequest($request);

        if ($loginForm->isSubmitted() && $loginForm->isValid()) {
            $user = $userRepository->findOneBy(['email' => $user->getEmail()]);

            if ($user === null) {
                $this->addFlash(
                    'notice',
                    'On dirait qu\'on ne se connait pas encore ! Comment tu t\'appelles ? Moi c\'est Gauthier ;)'
                );

                return $this->redirectToRoute('register');
            }

            $loginLinkDetails = $loginLinkHandler->createLoginLink($user);
            $notification = new LoginLinkNotification(
                $loginLinkDetails,
                'Welcome back !'
            );

            $recipient = new Recipient($user->getEmail());

            $notifier->send($notification, $recipient);

            return $this->render('security/login_link_sent.html.twig', ['email' => $user->getEmail()]);
        }

        return $this->renderForm('security/login.html.twig', ['loginForm' => $loginForm]);
    }

    #[Route('/register', name: 'register', methods: ['GET', 'POST'])]
    public function register(
        Request $request,
        LoginLinkAuthenticator $mainAuthenticator,
        UserAuthenticatorInterface $userAuthenticator,
        EntityManagerInterface $entityManager,
        NotifierInterface $notifier
    ) {
        $user = new User();

        $registrationForm = $this->createForm(RegistrationType::class, $user);
        $registrationForm->handleRequest($request);

        if ($registrationForm->isSubmitted() && $registrationForm->isValid()) {
            $entityManager->persist($user);
            $entityManager->flush();

            $notification = new Notification(
                'Welcome to my Kitchen !',
                ['email']
            );
            $notification->content(sprintf('
                Bienvenu %s !
            
                On se voit bientôt !', $user->getFirstname()));

            $recipient = new Recipient($user->getEmail());

            $notifier->send($notification, $recipient);

            return $userAuthenticator->authenticateUser($user, $mainAuthenticator, $request);
        }

        return $this->renderForm('security/register.html.twig', ['registrationForm' => $registrationForm]);
    }

    #[Route('/logout', name: 'logout', methods: ['GET'])]
    public function logout()
    {
        throw new Exception('Don\'t forget to activate logout in security.yaml');
    }

    #[Route('/login_check', name: 'login_check')]
    public function check()
    {
        throw new LogicException('This code should never be reached');
    }
}
