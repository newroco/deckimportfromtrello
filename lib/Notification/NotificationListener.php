<?php

namespace OCA\DeckImportFromTrello\Notification;

use OCA\DeckImportFromTrello\Activity\FileImportEvent;

class NotificationListener
{
    protected $notificationManager;

    public function __construct()
    {
        $this->notificationManager = \OC::$server->get(\OCP\Notification\IManager::class);
    }

    public function handle(FileImportEvent $event)
    {
        $boardUrl = $event->getBoardUrl();
        $fileId = $event->getFileId();
        $userId = $event->getUserId();
        $userManager = \OC::$server->getUserManager();
        $user = $userManager->get($userId);

        $notification = $this->instantiateNotification($boardUrl, $fileId, $user->getDisplayName());

        $notification->setUser($userId);

        $this->notificationManager->notify($notification);
    }

    public function instantiateNotification($boardUrl, $fileId, $userDisplayName)
    {
        $notification = $this->notificationManager->createNotification();
        $acceptAction = $notification->createAction();
        $acceptAction->setLabel('view')
            ->setLink('deck', 'GET');

        $notification
            ->setApp('deckimportfromtrello')
            ->setObject('deckimportfromtrello', $fileId)
            ->setSubject('fileImport',['file_imported',
                $boardUrl,
                $fileId,
                $userDisplayName,
            ])
            ->addAction($acceptAction)
            ->setDateTime(new \DateTime());

        return $notification;
    }
}
