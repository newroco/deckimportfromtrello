<?php
namespace OCA\DeckImportFromTrello\BackgroundJob;

use \OCA\DeckImportFromTrello\Services\BackgroundImportToDeck;
use OCA\DeckImportFromTrello\Activity\EventHandler;
use OCA\DeckImportFromTrello\Activity\FileImportEvent;
use \OCP\BackgroundJob\QueuedJob;
use \OCP\AppFramework\Utility\ITimeFactory;
use \OCP\ILogger;
use OCP\IServerContainer;

class BackgroundJob extends QueuedJob  {
    private $backgroundImportToDeck;
    private $logger;
    private $eventHandler;

    public function __construct(ITimeFactory $time,
                                ILogger $logger,
                                IServerContainer $server,
                                EventHandler $eventHandler,
                                BackgroundImportToDeck $backgroundImportToDeck) {
        parent::__construct($time);
        $this->backgroundImportToDeck = $backgroundImportToDeck;
        $this->logger = $logger;
        $this->server = $server;
        $this->eventHandler = $eventHandler;
        //$this->logger->error('BackgroundImportToDeck failed: ');
    }

    public function run($arguments) {

        $boardUrl = ($this->server->getURLGenerator())->linkToRouteAbsolute('deck.board.index');
        $fileImportedEvent = new FileImportEvent(
            $boardUrl,
            'test',
            $arguments['file_id'],
            $arguments['user_id']
        );

        $this->eventHandler->handle($fileImportedEvent);

        //$this->logger->error('BackgroundImportToDeck failed: ' . $arguments['user_id']);
        /*try {
            $this->backgroundImportToDeck->doCron($arguments['file_id'],$arguments['user_id']);
        } catch (\Throwable $e) {
            $this->logger->warning('BackgroundImportToDeck failed: ' . $e->getMessage());
        }*/
    }

}