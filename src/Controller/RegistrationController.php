<?php

namespace App\Controller;

use App\Entity\Enum\UserRole;
use App\Entity\User;
use App\Form\RegistrationType;
use App\Notification\AttendeeNotification;
use App\Service\Notification\NotificationFactory;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Notifier\NotifierInterface;
use Symfony\Component\Notifier\Recipient\Recipient;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;
use Symfony\Component\Security\Http\Authenticator\LoginLinkAuthenticator;

class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'register', methods: ['GET', 'POST'])]
    public function register(
        Request $request,
        LoginLinkAuthenticator $mainAuthenticator,
        UserAuthenticatorInterface $userAuthenticator,
        EntityManagerInterface $entityManager,
        NotifierInterface $notifier,
        NotificationFactory $notificationFactory
    ) {
        $user = new User();
        $user->addRole(UserRole::ROLE_ATTENDEE);

        $registrationForm = $this->createForm(RegistrationType::class, $user);
        $registrationForm->handleRequest($request);

        if ($registrationForm->isSubmitted() && $registrationForm->isValid()) {
            $entityManager->persist($user);
            $entityManager->flush();

            $notification = $notificationFactory->createNotification(
                AttendeeNotification::class,
                'notification.register.subject',
                'notification.register.content',
                ['email']
            );

            $recipient = new Recipient($user->getEmail());

            $notifier->send($notification, $recipient);

            return $userAuthenticator->authenticateUser($user, $mainAuthenticator, $request);
        }

        return $this->renderForm('security/register.html.twig', ['registrationForm' => $registrationForm]);
    }
}
