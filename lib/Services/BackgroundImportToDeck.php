<?php
namespace OCA\DeckImportFromTrello\Services;

use OCA\DeckImportFromTrello\Activity\EventHandler;
use OCA\DeckImportFromTrello\Activity\FileImportEvent;
use \OCP\BackgroundJob\QueuedJob;
use OCP\IServerContainer;
use OCP\Files\IRootFolder;
use OCP\AppFramework\App;
use \OCP\Files\File;

class BackgroundImportToDeck {
    /**
     * @var
     */
    private $userId;
    private $eventHandler;
    protected $storage;
    private $deckImportFromTrelloService;

    protected $server;

    public function __construct(IServerContainer $server,
                                IRootFolder $storage,
                                EventHandler $eventHandler,
                                DeckImportFromTrelloService $deckImportFromTrelloService) {

        $this->server = $server;
        $this->storage = $storage;
        $this->eventHandler = $eventHandler;
        $this->deckImportFromTrelloService = $deckImportFromTrelloService;
    }

    /**
     * @param $fileId
     * @param $userId
     * @throws \Exception
     */
    public function doCron($fileId,$userId) {
        $this->userId = $userId;
        $this->deckImportFromTrelloService->setUserId($this->userId);
        $userFolder = $this->storage->getUserFolder($userId);
        // Read file contents
        $files = $userFolder->getById( (int) $fileId);
        $file = $files[0];

        if ( ! $file instanceof File) {
            throw new \Exception('Can not read from folder');
        }

        // Read JSON contents
        // Get board, lists and cards.
        $contents = $file->getContent();

        $board =  $this->deckImportFromTrelloService->parseJsonAndImport($contents);

        $boardUrl = ($this->server->getURLGenerator())->linkToRouteAbsolute('deck.board.index');
        $boardUrl = str_replace('/boards', '/#/board/' . $board->getId(), $boardUrl);

        $fileImportedEvent = new FileImportEvent(
            $boardUrl,
            $file->getName(),
            $file->getId(),
            $this->userId
        );

        $this->eventHandler->handle($fileImportedEvent);
    }

}