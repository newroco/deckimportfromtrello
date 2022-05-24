<?php

namespace OCA\DeckImportFromTrello\Activity;

use OCA\DeckImportFromTrello\Notification\NotificationListener;

class EventHandler
{
    private $activityListener;
    private $notificationListener;

    public function __construct(ActivityListener $activityListener, NotificationListener $notificationListener)
    {
        $this->activityListener = $activityListener;
        $this->notificationListener = $notificationListener;
    }

    public function handle(FileImportEvent $event)
    {
        $this->notificationListener->handle($event);
    }
}
