<?php

namespace OCA\DeckImportFromTrello\Activity;

use Symfony\Component\EventDispatcher\Event;

class FileImportEvent extends Event
{
    protected $boardUrl;
    protected $fileName;
    protected $fileId;
    protected $userId;

    public function __construct($boardUrl, $fileName, $fileId, $userId)
    {
        $this->boardUrl = $boardUrl;
        $this->fileName = $fileName;
        $this->fileId = $fileId;
        $this->userId = $userId;
    }

    public function getBoardUrl()
    {
        return $this->boardUrl;
    }

    public function getFileName()
    {
        return $this->fileName;
    }

    public function getFileId()
    {
        return $this->fileId;
    }

    public function getUserId()
    {
        return $this->userId;
    }
}
