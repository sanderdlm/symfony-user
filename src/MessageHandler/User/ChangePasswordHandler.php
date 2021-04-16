<?php

namespace App\MessageHandler\User;

use App\Message\User\ChangePassword;
use App\Repository\User\UserRepository;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

final class ChangePasswordHandler implements MessageHandlerInterface
{
    private UserRepository $userRepository;
    private UserPasswordEncoderInterface $passwordEncoder;

    public function __construct(
        UserRepository $userRepository,
        UserPasswordEncoderInterface $passwordEncoder,
    ) {
        $this->userRepository = $userRepository;
        $this->passwordEncoder = $passwordEncoder;
    }

    public function __invoke(ChangePassword $message): void
    {
        $encodedPassword = $this->passwordEncoder->encodePassword($message->getUser(), $message->password);

        $message->getUser()->setPassword($encodedPassword);

        $this->userRepository->save();
    }
}
