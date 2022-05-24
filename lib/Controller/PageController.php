<?php

namespace OCA\DeckImportFromTrello\Controller;

use Httpful\Request;
use OCA\DeckImportFromTrello\BackgroundJob\BackgroundJob;
use OCP\BackgroundJob\IJobList;
use OCP\AppFramework\Http\JSONResponse;
use OCP\IRequest;
use OCP\AppFramework\Controller;

class PageController extends Controller
{
    /**
     * @var
     */
    private $userId;
    protected $jobList;

    public function __construct(
        $AppName,
        IRequest $request,
        IJobList $jobList,
        $userId
    ) {
        parent::__construct($AppName, $request);
        $this->jobList = $jobList;
        $this->userId = $userId;
    }
    public function addJob(int $fileId,string $userId) {
        $this->jobList->add(BackgroundJob::class, ['file_id' => $fileId, 'user_id' => $userId]);
    }

    public function removeJob(int $fileId,string $userId) {
        $this->jobList->remove(BackgroundJob::class, ['file_id' => $fileId, 'user_id' => $userId]);
    }

    /**
     * @NoAdminRequired
     * @param $fileId
     * @return JSONResponse
     */
    public function store($fileId)
    {
        if (!$fileId) {
            return new JSONResponse([
                'message' => 'File required.',
            ], 403);
        }
        $userId = \OC::$server->getUserSession()->getUser()->getUID();
        $this->addJob($fileId,$userId);

        return new JSONResponse([
            'content' => 'success',
        ]);
    }
}
