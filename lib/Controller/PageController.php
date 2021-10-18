<?php

namespace OCA\DeckImportFromTrello\Controller;

use Httpful\Request;
use OCA\Deck\Service\BoardService;
use OCA\DeckImportFromTrello\Activity\FileImportEvent;
use OCA\DeckImportFromTrello\Db\File;
use OCA\DeckImportFromTrello\Services\DeckImportFromTrelloService;
use OCA\DeckImportFromTrello\Services\UserService;
use OCP\AppFramework\Http\JSONResponse;
use OCP\Files\IRootFolder;
use OCP\IRequest;
use OCP\IServerContainer;
use OCP\AppFramework\Controller;
use OCP\AppFramework\App;

class PageController extends Controller
{
    private $userId;
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
        DeckImportFromTrelloService $deckImportFromTrelloService,
        IRootFolder $storage,
        $UserId
    ) {
        parent::__construct($AppName, $request);

        $this->server = $server;
        $this->userId = $UserId;
        $this->storage = $storage;
        $this->deckImportFromTrelloService = $deckImportFromTrelloService;
    }

    /**
     * @NoAdminRequired
     * @param $id
     * @return JSONResponse
     */
    public function store($id)
    {
        if (!$id) {
            return new JSONResponse([
                'message' => 'File required.',
            ], 403);
        }

        $userFolder = $this->storage->getUserFolder($this->userId);

        try {
            // Read file contents
            $files = $userFolder->getById((int)$id);
            $file = $files[0];

            if ( ! $file instanceof \OCP\Files\File) {
                throw new StorageException('Can not read from folder');
            }

            // Read JSON contents
            // Get board, lists and cards.
            $contents = $file->getContent();

            $board = $this->deckImportFromTrelloService->parseJsonAndImport($contents);

//            $boardUrl = ($this->server->getURLGenerator())->linkToRouteAbsolute('deck.board.read', [
//                'boardId' => $board->getId()
//            ]);

            $boardUrl = ($this->server->getURLGenerator())->linkToRouteAbsolute('deck.board.index');
            $boardUrl = str_replace('/boards', '/#/board/' . $board->getId(), $boardUrl);

            $fileImportedEvent = new FileImportEvent(
                $boardUrl,
                $file->getName(),
                $file->getId(),
                UserService::getUser()
            );

            $eventHandler = (new App('deckimportfromtrello'))->getContainer()->query('OCA\DeckImportFromTrello\Activity\EventHandler');
            $eventHandler->handle($fileImportedEvent);

            return new JSONResponse([
                'content' => 'Success',
            ]);
        } catch (\Exception $exception) {
            return new JSONResponse([
                'message' => $exception->getMessage(),
            ], 403);
        }
    }
}
