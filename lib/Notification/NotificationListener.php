<?php

namespace OCA\DeckImportExport\Notification;

use OCA\DeckImportExport\Activity\FileImportEvent;

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
        $user = $event->getUser();

        $notification = $this->instantiateNotification($boardUrl, $fileId, $user->getDisplayName());

        $notification->setUser($user->getUID());

        $this->notificationManager->notify($notification);
    }

    public function instantiateNotification($boardUrl, $fileId, $user)
    {
        $notification = $this->notificationManager->createNotification();

        $acceptAction = $notification->createAction();
        $acceptAction->setLabel('view')
            ->setLink('deck', 'GET');

        $notification
            ->setApp('deckimportexport')
            ->setObject('deckimportexport', $fileId)
            ->setSubject('fileImport', [
                'file_imported',
                $boardUrl,
                $fileId,
                $user,
            ])
            ->addAction($acceptAction)
            ->setDateTime(new \DateTime());

        return $notification;
    }
}
