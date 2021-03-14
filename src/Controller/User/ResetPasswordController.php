<?php

namespace App\Controller\User;

use App\Entity\User\User;
use App\Form\User\ResetPasswordType;
use App\Message\User\ResetPassword;
use App\Repository\User\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class ResetPasswordController extends AbstractController
{
    /**
     * @Route("/reset-password/{token}", name="reset_password")
     */
    public function __invoke(
        string $token,
        Request $request,
        SessionInterface $session,
        UserRepository $userRepository
    ): Response {
        $user = $userRepository->findOneBy(['passwordResetToken' => $token]);

        if (!$user instanceof User) {
            $session->getBag('flashes')->add('error', 'invalid.token');

            return $this->redirectToRoute('login');
        }

        $form = $this->createForm(ResetPasswordType::class, new ResetPassword($user));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->dispatchMessage($form->getData());

            $session->getBag('flashes')->add('success', 'user.registered');

            return $this->redirectToRoute('login');
        }

        return $this->render('user/reset.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}