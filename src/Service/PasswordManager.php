<?php

namespace App\Service;

use App\Entity\User;
use App\Entity\UserPassword;
use App\Event\AppEvents;
use App\Event\UserEvent;
use App\Event\UserPasswordRequestEvent;
use DateTime;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class PasswordManager
{
    private ManagerRegistry $doctrine;
    private EventDispatcherInterface $dispatcher;

    public function __construct(
        ManagerRegistry $doctrine,
        EventDispatcherInterface $dispatcher
    ) {
        $this->doctrine = $doctrine;
        $this->dispatcher = $dispatcher;
    }

    public function createRequest(string $email): ?UserPassword
    {
        $user = $this->doctrine->getRepository(User::class)->findOneBy(['email' => $email]);

        if (null === $user) {
            return null;
        }

        $userPwd = (new UserPassword())
            ->setUser($user)
            ->setToken(hash('sha512', $user->getId().microtime().rand()))
            ->setExpireAt(new \DateTime('+ 1 hour'))
        ;

        $entityMgr = $this->doctrine->getManager();
        $entityMgr->persist($userPwd);
        $entityMgr->flush();

        $this->dispatcher->dispatch(
            new UserPasswordRequestEvent($userPwd),
            AppEvents::USER_PASSWORD_REQUEST
        );

        return $userPwd;
    }

    public function secureRequest(UserPassword $userPwd): UserPassword
    {
        $userPwd->setExpireAt(new \DateTime('+ 15 minutes'));

        $entityMgr = $this->doctrine->getManager();
        $entityMgr->persist($userPwd);
        $entityMgr->flush();

        return $userPwd;
    }

    public function cleanRequests()
    {
        return $this->doctrine
            ->getRepository(UserPassword::class)
            ->cleanExpired()
        ;
    }

    public function reset(UserPassword $userPwd)
    {
        $user = $userPwd->getUser();
        dump($user);

        $this->dispatcher->dispatch(
            new UserEvent($user),
            AppEvents::USER_PASSWORD_RESET
        );

        $entityMgr = $this->doctrine->getManager();
        $entityMgr->remove($userPwd);
        $entityMgr->persist($user);
        $entityMgr->flush();
    }
}
