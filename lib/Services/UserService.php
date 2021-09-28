<?php

namespace OCA\DeckImportExport\Services;

use OCP\IUser;

class UserService
{
    public static function get()
    {
        return \OC::$server->getUserSession()->getUser()->getUID();
    }

    public static function getUser()
    {
        $userManager = \OC::$server->getUserManager();

        $userId = static::get();
        $user = $userManager->get($userId);

        if ($user instanceof IUser) {
            return $user;
        }

        return $userId;
    }

    public static function getDisplayName($user)
    {

    }
}
