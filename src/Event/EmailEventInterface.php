<?php

namespace App\Event;

use App\Entity\User;

interface EmailEventInterface
{
    public function getUser(): User;

    /**
     * Return email template path.
     */
    public function getEmailTemplate(): string;

    /**
     * Return email template options.
     */
    public function getEmailOptions(): array;
}
