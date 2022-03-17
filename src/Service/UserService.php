<?php

namespace App\Service;

use App\Repository\UserRepository;

class UserService
{
    private UserRepository $userRepo;

    public function __construct(UserRepository $userRepo)
    {
        $this->userRepo = $userRepo;
    }

    public function generateRefererCode(): string
    {
        $finded = false;

        while (true !== $finded) {
            $code = strtoupper(substr(base_convert(sha1(uniqid(mt_rand())), 16, 36), 0, 10));
            $existing = $this->userRepo->findOneBy(['refererCode' => $code]);
            $finded = null === $existing;
        }

        return $code;
    }
}
