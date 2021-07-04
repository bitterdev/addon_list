<?php

/**
 * @project:   Addon list add-on for concrete5
 * 
 * @author     Fabian Bitter
 * @copyright  (C) 2017 Fabian Bitter (www.bitter.de)
 * @version    1.0
 */

namespace Concrete\Package\AddonList\Src;

defined('C5_EXECUTE') or die('Access denied');

use Concrete\Package\AddonList\Src\Helpers;
use Package;

class MarketplaceSettings {

    private $package;
    private static $instance = null;
    
    /**
     * @return MarketplaceSettings
     */
    public static function getInstance() {
        if (is_null(self::$instance)) {
            self::$instance = new self;
        }

        return self::$instance;
    }

    public function __construct() {
        $this->init();
    }
    
    public function init() {
        $this->package = Package::getByHandle('addon_list');
    }

    private function getSetting($keyName, $defaultValue) {
        return $this->package->getConfig()->get($keyName, $defaultValue);
    }

    private function setSetting($keyName, $value) {
        return $this->package->getConfig()->save($keyName, $value);
    }

    function getUsername() {
        return $this->getSetting("settings.username", "");
    }

    function getPassword() {
        $password = $this->getSetting("settings.password", "");
        
        return Helpers::decryptString($password);
    }

    function getMaskedPassword() {
        return str_repeat("*", strlen($this->getPassword()));
    }
    
    function getDemoContentInstalled() {
        return intval($this->getSetting("settings.demo_content_instaled", 0)) === 1;
    }
    
    function getOverviewPageId() {
        return intval($this->getSetting("settings.overview_page_id", 0));
    }
    
    function getDetailsPageId() {
        return intval($this->getSetting("settings.details_page_id", 0));
    }

    function getProfilePageUrl() {
        return $this->getSetting("settings.profile_page_url", '');
    }

    function setUsername($username) {
        return $this->setSetting("settings.username", $username);
    }

    function setProfilePageUrl($profilePageUrl) {
        return $this->setSetting("settings.profile_page_url", $profilePageUrl);
    }

    function getSyncStartTime() {
        return intval($this->getSetting("settings.sync_start_time", 0));
    }

    function setSyncStartTime($syncStartTime) {
        return $this->setSetting("settings.sync_start_time", intval($syncStartTime));
    }
    
    function setPassword($password) {
        $password = Helpers::encryptString($password);
        
        return $this->setSetting("settings.password", $password);
    }
    
    function setDemoContentInstalled($isInstalled) {
        return $this->setSetting("settings.demo_content_instaled", intval($isInstalled));
    }
    
    function setOverviewPageId($pageId) {
        return $this->setSetting("settings.overview_page_id", intval($pageId));
    }
    
    function setDetailsPageId($pageId) {
        return $this->setSetting("settings.details_page_id", intval($pageId));
    }

}
