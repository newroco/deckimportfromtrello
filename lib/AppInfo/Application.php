<?php

namespace OCA\DeckImportExport\AppInfo;
use OCP\AppFramework\App;

class Application extends App {
    const APP_ID = 'deckimportexport';

    public function __construct()
    {
        parent::__construct(self::APP_ID);
    }

    public function register()
    {
            $this->registerScripts();
    }

    protected function registerScripts()
    {
        $eventDispatcher = \OC::$server->getEventDispatcher();
        $eventDispatcher->addListener('OCA\Files::loadAdditionalScripts', function() {
            script(self::APP_ID, 'deckimportexport');
        });
    }
}