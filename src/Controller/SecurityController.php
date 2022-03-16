<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\LoginType;
use App\Notification\AttendeeNotification;
use App\Repository\UserRepository;
use App\Service\Notification\NotificationFactory;
use Exception;
use LogicException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Notifier\NotifierInterface;
use Symfony\Component\Notifier\Recipient\Recipient;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\LoginLink\LoginLinkHandlerInterface;

class SecurityController extends AbstractController
{
    #[Route('/login', name: 'login', methods: ['GET', 'POST'])]
    public function requestLoginLink(
        LoginLinkHandlerInterface $loginLinkHandler,
        UserRepository $userRepository,
        NotifierInterface $notifier,
        NotificationFactory $notificationFactory,
        Request $request
    ): Response {
        if ($request->isMethod('POST') && $request->request->has('email')) {
            $user = $userRepository->findOneBy(['email' => $request->request->get('email')]);

            if ($user === null) {
                $this->addFlash(
                    'notice',
                    'On dirait qu\'on ne se connait pas encore ! Comment tu t\'appelles ? Moi c\'est Gauthier ;)'
                );

                return $this->redirectToRoute('register');
            }

            if ($user->getEmail() === null) {
                throw new LogicException('This code should never be reached');
            }

            $loginLinkDetails = $loginLinkHandler->createLoginLink($user);
            /** @var AttendeeNotification $notification */
            $notification = $notificationFactory->createNotification(
                AttendeeNotification::class,
                'notification.login.link.subject',
                'notification.login.link.content',
                ['email'],
                ['firstname' => $user->getFirstname()],
                [
                    'date' => $loginLinkDetails->getExpiresAt()->format('d/m/Y'),
                    'time' => $loginLinkDetails->getExpiresAt()->format('H:i')
                ],
            );

            $notification->setAction('notification.login.link.action', $loginLinkDetails->getUrl());

            $recipient = new Recipient($user->getEmail());

            $notifier->send($notification, $recipient);

            return $this->render('security/login_link_sent.html.twig', ['email' => $user->getEmail()]);
        }

        return $this->render('security/login.html.twig');
    }

    #[Route('/logout', name: 'logout', methods: ['GET'])]
    public function logout(): void
    {
        throw new Exception('Don\'t forget to activate logout in security.yaml');
    }

    #[Route('/login_check', name: 'login_check')]
    public function check(): void
    {
        throw new LogicException('This code should never be reached');
    }
}
