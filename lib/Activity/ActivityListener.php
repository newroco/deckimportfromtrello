<?php

namespace OCA\DeckImportFromTrello\Activity;

use OCP\Activity\IManager;
use OCP\App\IAppManager;
use OCP\IUser;
use OCP\IUserSession;

class ActivityListener
{
    /** @var IManager */
    protected $activityManager;

    /** @var IUserSession */
    protected $session;

    /** @var \OCP\App\IAppManager */
    protected $appManager;

    /**
     * Listener constructor.
     *
     * @param IManager $activityManager
     * @param IUserSession $session
     * @param IAppManager $appManager
     */
    public function __construct(IManager $activityManager, IUserSession $session, IAppManager $appManager)
    {
        $this->activityManager = $activityManager;
        $this->session = $session;
        $this->appManager = $appManager;
    }

    /**
     * Generate the event and dispatch it.
     *
     * @param FileImportEvent $event
     */
    public function fileImported(FileImportEvent $event)
    {
        if ( ! $this->appManager->isInstalled('deckimportfromtrello')) {
            return;
        }

        $actor = $this->getActor();

        $activity = $this->activityManager->generateEvent();
        $activity->setApp('deckimportfromtrello')
            ->setType('deckimportfromtrello')
            ->setAuthor($actor)
//            ->setObject('file_imported', $event->getFileName())
            ->setMessage('file_imported', [
                'fileName' => $event->getFileName(),
            ])
            ->setAffectedUser($actor)
            ->setSubject('file_imported', [
                'actor' => $actor,
            ]);

        $this->activityManager->publish($activity);
    }

    /**
     * Get the user that acted upon the file.
     *
     * @return string
     */
    protected function getActor()
    {
        $actor = $this->session->getUser();

        if ($actor instanceof IUser) {
            return $actor->getUID();
        }

        return '';
    }
}
