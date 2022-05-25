<?php

namespace OCA\DeckImportFromTrello\Controller;

use Httpful\Request;
use OCA\Deck\Service\BoardService;
use OCA\DeckImportFromTrello\Activity\FileImportEvent;
use OCA\DeckImportFromTrello\Activity\EventHandler;
use OCA\DeckImportFromTrello\Services\DeckImportFromTrelloService;
use OCA\DeckImportFromTrello\Services\UserService;
use OCP\AppFramework\Http\JSONResponse;
use OCP\Files\IRootFolder;
use OCP\IRequest;
use OCP\IServerContainer;
use OCP\AppFramework\Controller;
use OCP\AppFramework\App;
use OCP\Files\File;

class PageController extends Controller
{
    private $userId;
    private $eventHandler;
    protected $storage;
    protected $server;
    /**
     * @var DeckImportFromTrelloService
     */
    private $deckImportFromTrelloService;


    public function __construct(
        $AppName,
        IRequest $request,
        IServerContainer $server,
        IRootFolder $storage,
        EventHandler $eventHandler,
        DeckImportFromTrelloService $deckImportFromTrelloService,

        $UserId
    ) {
        parent::__construct($AppName, $request);

        $this->server = $server;
        $this->userId = $UserId;
        $this->storage = $storage;
        $this->eventHandler = $eventHandler;
        $this->deckImportFromTrelloService = $deckImportFromTrelloService;
    }

    /**
     * @NoAdminRequired
     * @param $id
     * @return JSONResponse
     */
    public function store($fileId)
    {
        if (!$fileId) {
            return new JSONResponse([
                'message' => 'File required.',
            ], 403);
        }

        $userFolder = $this->storage->getUserFolder($this->userId);

        try {
            // Read file contents
            $files = $userFolder->getById((int)$fileId);
            $file = $files[0];

            if ( ! $file instanceof File) {
                throw new StorageException('Can not read from folder');
            }

            // Read JSON contents
            // Get board, lists and cards.
            $contents = $file->getContent();

            $board = $this->deckImportFromTrelloService->parseJsonAndImport($contents);
            $boardUrl = ($this->server->getURLGenerator())->linkToRouteAbsolute('deck.board.index');
            $boardUrl = str_replace('/boards', '/#/board/' . $board->getId(), $boardUrl);

            $fileImportedEvent = new FileImportEvent(
                $boardUrl,
                $file->getName(),
                $file->getId(),
                UserService::getUser()
            );

            $this->eventHandler->handle($fileImportedEvent);

            return new JSONResponse([
                'content' => 'Success',
                'boardUrl' => $boardUrl
            ]);
        } catch (\Exception $exception) {
            return new JSONResponse([
                'message' => $exception->getMessage(),
            ], 403);
        }
    }
}
