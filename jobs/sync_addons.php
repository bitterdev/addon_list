<?php

/**
 * @project:   Addon list add-on for concrete5
 * 
 * @author     Fabian Bitter
 * @copyright  (C) 2017 Fabian Bitter (www.bitter.de)
 * @version    1.0
 */

namespace Concrete\Package\AddonList\Job;

defined('C5_EXECUTE') or die('Access denied');

use Concrete\Core\Job\QueueableJob as AbstractJob;
use Concrete\Package\AddonList\Src\MarketplaceAddons;
use Log;

class SyncAddons extends AbstractJob {

    public function getJobName() {
        return t("Synchronize add-ons");
    }

    public function getJobDescription() {
        return t("Synchronize your add-ons from your concrete5 account with your local database.");
    }

    public function start(\ZendQueue\Queue $q) {
        $syncTool = MarketplaceAddons::getInstance();
        
        $urlSet = $syncTool->startSync();
        
        if ($syncTool->hasErrors() === false) {
            foreach($urlSet as $url) {
                $q->send($url);
            }
        } else {
            foreach ($syncTool->getErrorList()->getList() as $error) {
                // log error
                Log::addError($error->getMessage(), array($error));

                // display error
                throw new \Exception($error->getMessage());
            }
        }
    }

    public function finish(\ZendQueue\Queue $q) {
        $syncTool = MarketplaceAddons::getInstance();

        $syncTool->finishSync();
    }

    public function processQueueItem(\ZendQueue\Message $msg) {
        $syncTool = MarketplaceAddons::getInstance();
        
        $marketplaceItemUrl = $msg->body;
        
        $syncTool->processMarketplaceItemUrl($marketplaceItemUrl);
        
        if ($syncTool->hasErrors()) {
            foreach ($syncTool->getErrorList()->getList() as $error) {
                // log error
                Log::addError($error->getMessage(), array($error));

                // display error
                throw new \Exception($error->getMessage());
            }
        }
    }
}