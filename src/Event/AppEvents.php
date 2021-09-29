<?php

namespace App\Event;

final class AppEvents
{
    /**
     * @Event("App\Event\UserPasswordRequestEvent")
     */
    const USER_PASSWORD_REQUEST = 'user.password.request.event';

    /**
     * @Event("App\Event\UserEvent")
     */
    const USER_PASSWORD_RESET = 'user.password.reset.event';
}
