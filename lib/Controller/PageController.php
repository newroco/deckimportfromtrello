<?php
namespace OCA\DeckImportExport\Controller;

use Httpful\Request;
use OCA\Deck\Service\BoardService;
use OCA\DeckImportExport\Db\File;
use OCA\DeckImportExport\Services\DeckImportExportService;
use OCP\AppFramework\Http\JSONResponse;
use OCP\Files\IRootFolder;
use OCP\IRequest;
use OCP\AppFramework\Controller;

class PageController extends Controller {
	private $userId;
	protected $storage;
    /**
     * @var DeckImportExportService
     */
    private $deckImportExportService;


    public function __construct($AppName, IRequest $request,DeckImportExportService $deckImportExportService,IRootFolder $storage,$UserId){
		parent::__construct($AppName, $request);
		$this->userId = $UserId;
        $this->storage = $storage;
        $this->deckImportExportService = $deckImportExportService;
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
            if($file instanceof \OCP\Files\File) {
                $contents = $file->getContent();
                return  $this->deckImportExportService->parseJsonAndImport($contents,$this->userId);

            } else {
                throw new StorageException('Can not read from folder');
            }

            // Read JSON contents
            // Get board, lists and cards.
        } catch (\Exception $exception) {
            return new JSONResponse([
                'message' => $exception->getMessage(),
            ], 403);
        }

        return new JSONResponse([
            'success' => true
        ]);
    }

}
