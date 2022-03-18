<?php

namespace OCA\DeckImportFromTrello\Notification;

use OCP\Files\IRootFolder;
use OCP\IURLGenerator;
use OCP\IUserManager;
use OCP\L10N\IFactory;
use OCP\Notification\INotification;
use OCP\Notification\INotifier;

class Notifier implements INotifier {

    /** @var IFactory */
    protected $l10nFactory;

    /** @var IRootFolder  */
    protected $rootFolder;

    /** @var IURLGenerator */
    protected $url;

    /** @var IUserManager */
    protected $userManager;

    public function __construct(
        IFactory $l10nFactory,
        IRootFolder $rootFolder,
        IURLGenerator $url,
        IUserManager $userManager
    ) {
        $this->l10nFactory = $l10nFactory;
        $this->rootFolder = $rootFolder;
        $this->url = $url;
        $this->userManager = $userManager;
    }

    /**
     * @param INotification $notification
     * @param string $languageCode The code of the language that should be used to prepare the notification
     * @return INotification
     */
    public function prepare(INotification $notification, string $languageCode): INotification
    {
        if ($notification->getApp() !== 'deckimportfromtrello') {
            throw new \InvalidArgumentException();
        }

        $l = $this->l10nFactory->get('deckimportfromtrello', $languageCode);

        $parameters = $notification->getSubjectParameters();

        if ($parameters[0] !== 'file_imported') {
            throw new \InvalidArgumentException('Unsupported file imported object.');
        }

        $boardUrl = $parameters[1];

        $notifiedUser = $notification->getUser();
        $fileId = (int)$parameters[2];
        $userFolder = $this->rootFolder->getUserFolder($notifiedUser);
        $nodes = $userFolder->getById($fileId);

        if (empty($nodes)) {
            throw new \InvalidArgumentException('Cannot resolve file ID to node instance');
        }

        $file = $nodes[0];

        $notification->setParsedSubject(
            $l->t(
                'Imported to Deck from file "%1$s" successfully.',
                [$file->getName()]
            )
        )->setRichSubject(
            $l->t('Imported to Deck from file {file} successfully.'),
            [
                'file' => [
                    'type' => 'file',
                    'id' => $fileId,
                    'name' => $file->getName(),
                    'path' => $file->getPath(),
                    'link' => $this->url->linkToRouteAbsolute('files.viewcontroller.showFile', ['fileid' => $fileId]),
                ],
            ]
        );

        $notification->setIcon(
            $this->url->getAbsoluteURL($this->url->imagePath('deckimportfromtrello', 'app.svg'))
        );

        $action = $notification->getActions()[0];

        $action->setParsedLabel($l->t('View board'))
            ->setLink($boardUrl, 'WEB');

        $notification->addParsedAction($action);

        return $notification;
    }

    public function getID(): string
    {
        return 'deckimportfromtrello';
    }

    public function getName(): string
    {
        return $this->lFactory->get('deckimportfromtrello')->t('deckimportfromtrello');
    }
}
