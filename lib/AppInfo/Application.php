<?php

namespace OCA\DeckImportFromTrello\AppInfo;

use OCA\DeckImportFromTrello\Notification\Notifier;
use OCA\DeckImportFromTrello\Notification\NotificationListener;
use OCP\AppFramework\App;
use OCP\AppFramework\Bootstrap\IBootstrap;
use OCP\AppFramework\Bootstrap\IBootContext;
use OCP\AppFramework\Bootstrap\IRegistrationContext;
use OCP\Notification\IManager;

class Application extends App implements IBootstrap
{
    const APP_ID = 'deckimportfromtrello';

    public function __construct()
    {
        parent::__construct(self::APP_ID);
    }

    public function boot(IBootContext $context): void
    {
        if ( ! \OC::$server->getAppManager()->isEnabledForUser(self::APP_ID)) {
            return;
        }

        $container = $this->getContainer();

        $this->registerHooks($context);
        $this->registerScripts();
        $this->registerVarious($container);
    }

    public function register(IRegistrationContext $context): void
    {

    }

    public function registerHooks($context)
    {
        $manager = $context->getAppContainer()->query(IManager::class);
        $manager->registerNotifierService(Notifier::class);
    }

    protected function registerScripts()
    {
        $eventDispatcher = \OC::$server->getEventDispatcher();
        $eventDispatcher->addListener('OCA\Files::loadAdditionalScripts', function() {
            script(self::APP_ID, 'deckimportfromtrello');
        });
    }

    protected function registerVarious($container)
    {
//        $container->registerService('ActivityListener', function(ContainerInterface $c){
//            return new ActivityListener(
//                $c->query(IManager::class),
//                $c->query(IUserSession::class),
//                $c->query(IAppManager::class)
//            );
//        });

        $container->registerService('NotificationListener', function(ContainerInterface $c){
            return new NotificationListener();
        });
    }
}
