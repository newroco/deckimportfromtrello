<?php

namespace OCA\DeckImportExport\Activity;

use Symfony\Component\EventDispatcher\Event;

class FileImportEvent extends Event
{
    protected $boardUrl;
    protected $fileName;
    protected $fileId;
    protected $user;

    public function __construct($boardUrl, $fileName, $fileId, $user)
    {
        $this->boardUrl = $boardUrl;
        $this->fileName = $fileName;
        $this->fileId = $fileId;
        $this->user = $user;
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

    public function getUser()
    {
        return $this->user;
    }
}
