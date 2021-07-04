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

use Doctrine\ORM\Tools\Pagination\Paginator;
use Concrete\Package\AddonList\Src\Entity\MarketplaceAddon;
use Concrete\Package\AddonList\Src\Entity\MarketplaceAddonImage;
use Concrete\Package\AddonList\Src\MarketplaceSettings;
use Concrete\Package\AddonList\Src\Helpers;
use PhpQuery\PhpQueryObject;
use Database;
use Log;
use Core;

class MarketplaceAddons {
    
    const MARKETPLACE_TYPE_LEGACY = 0;
    const MARKETPLACE_TYPE_MODERN = 1;
    const MARKETPLACE_TYPE_BOTH = 2;
    
    private $settings;
    private $errorList;
    private $db;
    private $em;
    private $isDebugMode;
    private $debugProfileUrl;
    
    private static $instance = null;
    
    private function getLastError() {
        $errorList = $this->getErrorList()->getList();
                
        return $errorList[count($errorList) - 1];
    }
    
    private function raiseError() {
        Log::addEntry($this->getLastError());
        
        if ($this->isDebugMode()) {
            throw new \Exception($this->getLastError());
        }
    }
    
    private function isDebugMode() {
        return $this->isDebugMode;
    }
    
    private function getDebugProfileUrl() {
        return $this->debugProfileUrl;
    }
    
    public function enableDebugMode($profilePageUrl) {
        $this->isDebugMode = true;
        $this->debugProfileUrl = $profilePageUrl;
    }
    
    public function disableDebugMode($profilePageUrl) {
        $this->isDebugMode = false;
    }
    
    public function __construct() {
        $this->settings = MarketplaceSettings::getInstance();
        $this->errorList = Core::make('helper/validation/error');
        $this->db = Database::connection();
        $this->em = $this->db->getEntityManager();
    }
    
    /**
     * @return MarketplaceAddons
     */
    public static function getInstance() {
        if (is_null(self::$instance)) {
            self::$instance = new self;
        }

        return self::$instance;
    }
    
    /**
     * @return Concrete\Core\Error\ErrorList\ErrorList
     */
    public function getErrorList() {
        return $this->errorList;
    }
    
    /**
     * @param string $username
     * @param string $password
     * 
     * @return boolean
     */
    public function verifyCredentials($username, $password) {
        $success = $this->login($username, $password);
        
        $this->logout();
        
        return $success;
    }
    /**
     * @param string $username
     * @param string $password
     * 
     * @return boolean
     */
    public function login($username = null, $password = null) {
        
        // reset errorlist object
        $this->errorList = Core::make('helper/validation/error');
        
        if (is_null($username)) {
            $username = $this->settings->getUsername();
            
        }
        
        if (is_null($password)) {
            $password = $this->settings->getPassword();
        }
        
        try {
            // login to concrete5
            $doc = Helpers::fetchDOM(
                "https://www.concrete5.org/login/-/do_login/",

                array(
                    "ccm_token" => Helpers::fetchDOM("https://www.concrete5.org/login/")->find("input[name=ccm_token]")->val(),
                    "uName" => $username,
                    "uPassword" => $password,
                    "rcID" => ""
                )
            );
            
            if ($doc->find(".alert-error")->length === 0) {
                $profilePageUrl = "https://www.concrete5.org" . $doc->find("a[href^='/profile/-/view']")->attr("href");
                
                // save url for further processing
                $this->settings->setProfilePageUrl($profilePageUrl);
                
                if ($this->isDebugMode()) {
                    $this->settings->setProfilePageUrl($this->getDebugProfileUrl());
                }
            }
            
        } catch (\Exception $e) {
            $this->logout();
            
            // some error happend
            $this->errorList->add($e->getMessage());
            
            $this->raiseError();
        } 
        
        return $this->errorList->has() === false;
    }
    
    public function hasErrors() {
        return $this->errorList->has();
    }
    
    public function logout() {
        try {
            // logout from concrete5
            Helpers::fetchUrl("https://www.concrete5.org/logout");

            // remove cookie.txt when finish for security reasons (sesssion hijacking)
            Core::make('helper/file')->clear(Helpers::getTempFile());
            
        } catch (\Exception $e) {
            $this->logout();
            
            // some error happend
            $this->errorList->add($e->getMessage());
            
            $this->raiseError();
        } 
        
        return $this->errorList->has() === false;
    }
    
    public function fetchSaleStats() {
        try { 
            foreach(Helpers::fetchDOM("https://www.concrete5.org/profile/sales_statistics/")->find("fieldset a") as $a) {
                $slug = substr(\PhpQuery\PhpQuery::pq($a)->attr("href"), 20);

                if (is_object($item = $this->em->getRepository('Concrete\Package\AddonList\Src\Entity\MarketplaceAddon')->findOneBy(array("slug" => $slug))) === false) {
                    $item = new MarketplaceAddon;
                }

                $item->setSlug($slug);
                $item->setDownloadStats(intval(\PhpQuery\PhpQuery::pq($a)->parent()->next()->text()));

                $this->em->persist($item);
                $this->em->flush();
            }
            
        } catch (\Exception $e) {
            $this->logout();
            
            // some error happend
            $this->errorList->add($e->getMessage());
            
            $this->raiseError();
        } 
        
        return $this->errorList->has() === false;
    }
    
    /**
     * @return array
     */
    public function startSync() {
        $urlSet = array();
        
        $this->settings->setSyncStartTime(time());
        
        if ($this->login()) {
            $this->fetchSaleStats();
            
            $this->logout();
            
            $urlSet = $this->getMarketplaceItemUrls();
        }
        
        return $urlSet;
    }
    
    public function finishSync() {
        // remove all old items from database
        $this->db->executeQuery("DELETE FROM MarketplaceAddonImage WHERE lastSync < ?", array($this->settings->getSyncStartTime()));
        $this->db->executeQuery("DELETE FROM MarketplaceAddon WHERE lastSync < ?", array($this->settings->getSyncStartTime()));
    }
    
    /**
     * @param mixed $maxItems
     * 
     * @return boolean
     */
    public function syncItemsSynchronously($maxItems = false) {
        $urlSet = $this->startSync();
        
        $importCounter = 0;
        
        foreach($urlSet as $url) {
            $importCounter++;
            
            if ($maxItems === false || $importCounter <= $maxItems) {
                $this->processMarketplaceItemUrl($url);
            }
        }
        
        $this->finishSync();
        
        return $this->errorList->has() === false;
    }
    
    /**
     * 
     * @param string $url
     * @return string
     */
    private function cleanUrl($url) {
        $url = trim($url);
        
        if ($url === "#") {
            $url = "";
        } else if (strlen($url) > 8 && (substr($url, 0, 7) === "http://" || substr($url, 0, 8) === "https://") === false) {
            $url = "https://www.concrete5.org" . $url;
        }
        
        return $url;
    }
    
    /**
     * 
     * @param MarketplaceAddon $item
     * @param PhpQueryObject $doc
     * 
     * @return boolean
     */
    private function processModernMarketplaceAddon($item, $doc) {
        try {
            // concrete5 version >= 5.7 (Modern Marketplace)
            switch(trim($doc->find(".skill-level-title")->text())) {
                case "Beginner":
                    $item->setSkillLevel(MarketplaceAddon::SKILL_LEVEL_BEGINNER);
                    break;

                case "Intermediate":
                    $item->setSkillLevel(MarketplaceAddon::SKILL_LEVEL_INTERMEDIATE);
                    break;

                case "Expert":
                    $item->setSkillLevel(MarketplaceAddon::SKILL_LEVEL_EXPERT);
                    break;

                case "Bleeding Edge":
                    $item->setSkillLevel(MarketplaceAddon::SKILL_LEVEL_BLEEDING_EDGE);
                    break;
            }

            $item->setIsLegacy(false);
            $item->setShortDescription(trim($doc->find("meta[name=description]")->attr("content")));
            $item->setTitle(trim($doc->find(".info-name")->text()));
            $item->setShowcaseUrl($this->cleanUrl($doc->find(".info-box .info-footer li:eq(3) a")->attr("href")));
            $item->setLiveDemoUrl($this->cleanUrl($doc->find(".info-box .info-footer li:eq(2) a")->attr("href")));
            $item->setProfileUrl($this->cleanUrl($doc->find(".info-author a")->attr("href")));
            $item->setVideoEmbedCode(trim($doc->find(".video-template")->html()));
            $item->setStarRatingAverage(floatval(trim($doc->find(".rating")->attr("data-score"))) / 100 * 5);
            $item->setStarRatingCount(intval(explode(" ", $doc->find(".rating")->next()->children("a")->text())[0]));
            $item->setDescription(trim($doc->find(".addon-content")->html()));
            $item->setIconUrl($this->cleanUrl($doc->find(".marketplace-thumbnail")->attr("src")));
            $item->setPackagePrice(floatval(substr(trim($doc->find(".purchase-pack .price")->text()), 1)));
            $item->setSinglePrice(floatval(substr(trim($doc->find(".purchase-single .price")->text()), 1)));
            $item->setVersionNumber(trim($doc->find(".addon-specs .col-left span:eq(1)")->text()));
            $item->setFullyTranslatable(trim($doc->find(".addon-specs .col-left span:eq(3)")->text()) === "Yes");
            $item->setNeedsExternalLibraries(trim($doc->find(".addon-specs .col-left span:eq(5)")->text()) === "Yes");
            $item->setCompatibleConcreteVersion(trim($doc->find(".addon-specs .col-left span:eq(7)")->text()));
            $item->setLicenseName(trim($doc->find(".addon-specs .col-left span:eq(9) a")->text()));
            $item->setLicenseUrl($this->cleanUrl(trim($doc->find(".addon-specs .col-left span:eq(9) a")->attr("href"))));
            $item->setSupportResponse(trim($doc->find(".addon-specs .col-right span:eq(1)")->text()));
            $item->setSupportHosted(trim($doc->find(".addon-specs .col-right span:eq(3)")->text()));
            $item->setNeedsExtraServerPermissions(trim($doc->find(".addon-specs .col-right span:eq(5)")->text()) === "Yes");
            $item->setNeedsInternet(trim($doc->find(".addon-specs .col-right span:eq(7)")->text()) === "Yes");
            $item->setPassedAutomatedTests(true);
            $item->setPassedPrbReview(true);
            $item->setSingleSKU(intval(str_replace("/", "", substr($doc->find(".purchase-single a[href^='/cart/-/add/']")->attr("href"), 12))));
            $item->setPackageSKU(intval(str_replace("/", "", substr($doc->find(".purchase-pack a[href^='/cart/-/add/']")->attr("href"), 12))));
            $item->setPackageDiscount(intval(trim(str_replace(array("Save", "%"), "", $doc->find(".savings strong")->text()))));
            $item->setQuoteReview(trim(Helpers::getStringTo($doc->find(".left-quote")->html(), "<br")));
            $item->setQuoteAuthor(trim($doc->find(".left-quote .review-author span")->text()));
            $item->setQuoteLink($this->cleanUrl(trim($doc->find(".left-quote .review-author")->attr("href"))));

            $item->setLastSync($this->settings->getSyncStartTime());

            $screenshotUrls = json_decode(Helpers::getStringBetween($doc->html(), "var screenshots = ", ";"));

            if (is_array($screenshotUrls) && count($screenshotUrls) > 0) {
                foreach($screenshotUrls as $screenshotUrl) {
                    if (is_object($image = $this->em->getRepository('Concrete\Package\AddonList\Src\Entity\MarketplaceAddonImage')->findOneBy(array("slug" => $item->getSlug(), "imageUrl" => $screenshotUrl))) === false) {
                        $image = new MarketplaceAddonImage;
                    }

                    $image->setSlug($item->getSlug());
                    $image->setImageUrl($screenshotUrl);
                    $image->setLastSync($this->settings->getSyncStartTime());

                    // add image entity to database
                    $this->em->persist($image);
                    $this->em->flush();
                }

            } else {
                // warning: no screenshots available for the current item
            }

            // add item entity to database
            $this->em->persist($item);
            $this->em->flush();
            
        } catch (\Exception $e) {
            $this->logout();
            
            // some error happend
            $this->errorList->add($e->getMessage());
            
            $this->raiseError();
        } 
        
        return $this->errorList->has() === false;
    }
    
    /**
     * 
     * @param MarketplaceAddon $item
     * @param PhpQueryObject $doc
     * 
     * @return boolean
     */
    private function processLegacyMarketplaceAddon($item, $doc) {
        try {
            // concrete5 version <= 5.6 (Legacy Marketplace)

            switch(trim($doc->find(".thumbnail-badge-grid img:eq(1)")->attr("alt"))) {
                case "Beginner":
                    $item->setSkillLevel(MarketplaceAddon::SKILL_LEVEL_BEGINNER);
                    break;

                case "Intermediate":
                    $item->setSkillLevel(MarketplaceAddon::SKILL_LEVEL_INTERMEDIATE);
                    break;

                case "Expert":
                    $item->setSkillLevel(MarketplaceAddon::SKILL_LEVEL_EXPERT);
                    break;

                case "Bleeding Edge":
                    $item->setSkillLevel(MarketplaceAddon::SKILL_LEVEL_BLEEDING_EDGE);
                    break;
            }

            $item->setIsLegacy(true);
            $item->setShortDescription(trim($doc->find("meta[name=description]")->attr("content")));
            $item->setTitle(trim($doc->find(".span6 h1")->text()));
            $item->setLiveDemoUrl($doc->find("#marketplace-addon-detail-icons ul li:eq(2) a")->attr("href"));
            $item->setShowcaseUrl($this->cleanUrl($this->cleanUrl($doc->find("#marketplace-addon-detail-icons ul li:eq(3) a")->attr("href"))));
            $item->setSinglePrice(floatval(explode(" ", substr(trim($doc->find("#marketplace-addon-detail-purchase-buttons-inner a:eq(0) span")->text()), 1))[0]));
            $item->setPackagePrice(floatval(explode(" ", substr(trim($doc->find("#marketplace-addon-detail-purchase-buttons-inner a:eq(1) span")->text()), 1))[0]));
            $item->setVersionNumber(trim(Helpers::getStringTo(Helpers::getStringBetween($doc->find("#marketplace-metadata div:eq(0)"), "</h3>", "</div>"), "<a")));
            $item->setCompatibleConcreteVersion(trim(Helpers::getStringBetween($doc->find("#marketplace-metadata div:eq(1)"), "</h3>", "</div>")));
            $item->setLicenseUrl($this->cleanUrl($doc->find("section ul.nav li:eq(2) a")->attr("href")));
            $item->setLicenseName(trim($doc->find("section ul.nav li:eq(2) a")->text()));
            $item->setDescription(trim($doc->find(".formatted-text")->html()));
            $item->setStarRatingAverage($doc->find(".ccm-rating .rating-star-on")->length);
            $item->setStarRatingCount(intval(explode(" ", $doc->find(".marketplace-reviews span a")->text())[0]));
            $item->setIconUrl($this->cleanUrl($doc->find(".marketplace-thumbnail")->attr("src")));
            $item->setProfileUrl($this->cleanUrl($doc->find(".marketplace-byline a")->attr("href")));
            $item->setSingleSKU(intval(str_replace("/", "", substr($doc->find(".btn.cart-button:not(.site-button-blue)")->attr("href"), 12))));
            $item->setPackageSKU(intval(str_replace("/", "", substr($doc->find(".btn.cart-button.site-button-blue")->attr("href"), 12))));
            $item->setSupportResponse(t("Unknown"));
            $item->setSupportHosted(trim($doc->find("#marketplace-addon-detail-purchase-more h3:eq(1) + ul li")->text()));
            $item->setPassedAutomatedTests(true);
            $item->setPassedPrbReview(true);
            $item->setLastSync($this->settings->getSyncStartTime());

            $screenshotUrls = array();

            foreach($doc->find(".grouped-elements") as $img) {
                $screenshotUrl = \PhpQuery\PhpQuery::pq($img)->attr("href");

                array_push($screenshotUrls, $screenshotUrl);
            }

            if (is_array($screenshotUrls) && count($screenshotUrls) > 0) {
                foreach($screenshotUrls as $screenshotUrl) {
                    if (is_object($image = $this->em->getRepository('Concrete\Package\AddonList\Src\Entity\MarketplaceAddonImage')->findOneBy(array("slug" => $item->getSlug(), "imageUrl" => $screenshotUrl))) === false) {
                        $image = new MarketplaceAddonImage;
                    }

                    $image->setSlug($item->getSlug());
                    $image->setImageUrl($screenshotUrl);
                    $image->setLastSync($this->settings->getSyncStartTime());

                    // add image entity to database
                    $this->em->persist($image);
                    $this->em->flush();
                }

            } else {
                // warning: no screenshots available for the current item
            }

            // add item entity to database
            $this->em->persist($item);
            $this->em->flush();
            
        } catch (\Exception $e) {
            $this->logout();
            
            // some error happend
            $this->errorList->add($e->getMessage());
        } 
        
        return $this->errorList->has() === false;
    }
    
    /**
     * 
     * @param MarketplaceAddon $item
     * @param PhpQueryObject $doc
     * 
     * @return boolean
     */
    private function processModernMarketplaceTheme($item, $doc) {
        try {
            // concrete5 version >= 5.7 (Modern Marketplace)
            switch(trim($doc->find(".skill-level-title")->text())) {
                case "Beginner":
                    $item->setSkillLevel(MarketplaceAddon::SKILL_LEVEL_BEGINNER);
                    break;

                case "Intermediate":
                    $item->setSkillLevel(MarketplaceAddon::SKILL_LEVEL_INTERMEDIATE);
                    break;

                case "Expert":
                    $item->setSkillLevel(MarketplaceAddon::SKILL_LEVEL_EXPERT);
                    break;

                case "Bleeding Edge":
                    $item->setSkillLevel(MarketplaceAddon::SKILL_LEVEL_BLEEDING_EDGE);
                    break;
            }

            $item->setIsLegacy(false);
            $item->setMainThemeImage($this->cleanUrl($doc->find(".main-theme-image img")->attr("src")));
            $item->setShortDescription(trim($doc->find("meta[name=description]")->attr("content")));
            $item->setTitle(trim($doc->find(".info-name")->text()));
            $item->setShowcaseUrl($this->cleanUrl($doc->find(".info-box .info-footer li:eq(3) a")->attr("href")));
            $item->setLiveDemoUrl($this->cleanUrl($doc->find(".info-box .info-footer li:eq(2) a")->attr("href")));
            $item->setProfileUrl($this->cleanUrl($doc->find(".info-author a")->attr("href")));
            $item->setVideoEmbedCode(trim($doc->find(".video-template")->html()));
            $item->setStarRatingAverage(floatval(trim($doc->find(".rating")->attr("data-score"))) / 100 * 5);
            $item->setStarRatingCount(intval(explode(" ", $doc->find(".rating")->next()->children("a")->text())[0]));
            $item->setDescription(trim($doc->find(".theme-contentquote")->html()));
            $item->setIconUrl($this->cleanUrl($doc->find("meta[name='twitter:image']")->attr("content")));
            $item->setPackagePrice(floatval(substr(trim($doc->find(".purchase-pack .price")->text()), 1)));
            $item->setSinglePrice(floatval(substr(trim($doc->find(".purchase-single .price")->text()), 1)));
            $item->setHasCustomizableStyles(trim($doc->find(".theme-specs .col-left span:eq(1)")->text()) === "Yes");
            $item->setLicenseName(trim($doc->find(".theme-specs .col-left span:eq(3)")->text()));
            $item->setLicenseUrl($this->cleanUrl(trim($doc->find(".theme-specs .col-left span:eq(3) a")->attr("href"))));
            $item->setPageTypes(str_replace(array("<br>", "<br/>", "\r\n"), ", ", trim($doc->find(".theme-specs .col-left span:eq(5)")->html())));
            $item->setVersionNumber(trim($doc->find(".theme-specs .col-left span:eq(7)")->text()));
            $item->setCompatibleConcreteVersion(trim($doc->find(".theme-specs .col-right span:eq(1)")->text()));
            $item->setSupportResponse(trim($doc->find(".theme-specs .col-right span:eq(3)")->text()));
            $item->setSupportHosted(trim($doc->find(".theme-specs .col-right span:eq(5)")->text()));
            $item->setPassedAutomatedTests(true);
            $item->setPassedPrbReview(true);
            $item->setSingleSKU(intval(str_replace("/", "", substr($doc->find(".purchase-single a[href^='/cart/-/add/']")->attr("href"), 12))));
            $item->setPackageSKU(intval(str_replace("/", "", substr($doc->find(".purchase-pack a[href^='/cart/-/add/']")->attr("href"), 12))));
            $item->setPackageDiscount(intval(trim(str_replace(array("Save", "%"), "", $doc->find(".savings strong")->text()))));
            $item->setQuoteReview(trim(Helpers::getStringTo($doc->find(".left-quote")->html(), "<br")));
            $item->setQuoteAuthor(trim($doc->find(".left-quote .review-author span")->text()));
            $item->setQuoteLink($this->cleanUrl(trim($doc->find(".left-quote .review-author")->attr("href"))));

            $item->setLastSync($this->settings->getSyncStartTime());

            $screenshotUrls = json_decode(Helpers::getStringBetween($doc->html(), "var screenshots = ", ";"));

            if (is_array($screenshotUrls) && count($screenshotUrls) > 0) {
                foreach($screenshotUrls as $screenshotUrl) {
                    if (is_object($image = $this->em->getRepository('Concrete\Package\AddonList\Src\Entity\MarketplaceAddonImage')->findOneBy(array("slug" => $item->getSlug(), "imageUrl" => $screenshotUrl))) === false) {
                        $image = new MarketplaceAddonImage;
                    }

                    $image->setSlug($item->getSlug());
                    $image->setImageUrl($screenshotUrl);
                    $image->setLastSync($this->settings->getSyncStartTime());

                    // add image entity to database
                    $this->em->persist($image);
                    $this->em->flush();
                }

            } else {
                // warning: no screenshots available for the current item
            }

            // add item entity to database
            $this->em->persist($item);
            $this->em->flush();
            
        } catch (\Exception $e) {
            $this->logout();
            
            // some error happend
            $this->errorList->add($e->getMessage());
            
            $this->raiseError();
        } 
        
        return $this->errorList->has() === false;
    }
    
    /**
     * 
     * @param MarketplaceAddon $item
     * @param PhpQueryObject $doc
     * 
     * @return boolean
     */
    private function processLegacyMarketplaceTheme($item, $doc) {
        
        try {
            // concrete5 version <= 5.6 (Legacy Marketplace)

            switch(trim($doc->find(".thumbnail-badge-grid img:eq(1)")->attr("alt"))) {
                case "Beginner":
                    $item->setSkillLevel(MarketplaceAddon::SKILL_LEVEL_BEGINNER);
                    break;

                case "Intermediate":
                    $item->setSkillLevel(MarketplaceAddon::SKILL_LEVEL_INTERMEDIATE);
                    break;

                case "Expert":
                    $item->setSkillLevel(MarketplaceAddon::SKILL_LEVEL_EXPERT);
                    break;

                case "Bleeding Edge":
                    $item->setSkillLevel(MarketplaceAddon::SKILL_LEVEL_BLEEDING_EDGE);
                    break;
            }
            
            $item->setIsLegacy(true);
            $item->setMainThemeImage($this->cleanUrl($doc->find(".ccm-output-thumbnail")->attr("src")));
            $item->setShortDescription(trim($doc->find("meta[name=description]")->attr("content")));
            $item->setTitle(trim($doc->find(".span6 h1")->text()));
            $item->setLiveDemoUrl($this->cleanUrl($doc->find("#marketplace-addon-detail-icons ul li:eq(2) a")->attr("href")));
            $item->setShowcaseUrl($this->cleanUrl($doc->find("#marketplace-addon-detail-icons ul li:eq(3) a")->attr("href")));
            $item->setSinglePrice(floatval(explode(" ", substr(trim($doc->find("#marketplace-addon-detail-purchase-buttons-inner a:eq(0) span")->text()), 1))[0]));
            $item->setPackagePrice(floatval(explode(" ", substr(trim($doc->find("#marketplace-addon-detail-purchase-buttons-inner a:eq(1) span")->text()), 1))[0]));
            $item->setVersionNumber(trim(Helpers::getStringTo(Helpers::getStringBetween($doc->find("#marketplace-metadata div:eq(0)"), "</h3>", "</div>"), "<a")));
            $item->setCompatibleConcreteVersion(trim(Helpers::getStringBetween($doc->find("#marketplace-metadata div:eq(1)"), "</h3>", "</div>")));
            $item->setHasCustomizableStyles(trim(Helpers::getStringBetween($doc->find("#marketplace-metadata div:eq(2)"), "</h3>", "</div>")) === "Yes");
            $item->setPageTypes(str_replace(array("<br>", "<br/>"), ",", trim(Helpers::getStringBetween($doc->find("#marketplace-metadata div:eq(3)"), "</h3>", "</div>"))));
            $item->setLicenseUrl($this->cleanUrl($doc->find("section ul.nav li:eq(2) a")->attr("href")));
            $item->setLicenseName(trim($doc->find("section ul.nav li:eq(2) a")->text()));
            $item->setDescription(trim($doc->find(".formatted-text")->html()));
            $item->setStarRatingAverage($doc->find(".ccm-rating .rating-star-on")->length);
            $item->setStarRatingCount(intval(explode(" ", $doc->find(".marketplace-reviews span a")->text())[0]));
            $item->setIconUrl($this->cleanUrl($doc->find(".marketplace-thumbnail")->attr("src")));
            $item->setProfileUrl($this->cleanUrl($doc->find(".marketplace-byline a")->attr("href")));
            $item->setSingleSKU(intval(str_replace("/", "", substr($doc->find(".btn.cart-button:not(.site-button-blue)")->attr("href"), 12))));
            $item->setPackageSKU(intval(str_replace("/", "", substr($doc->find(".btn.cart-button.site-button-blue")->attr("href"), 12))));
            $item->setSupportResponse(t("Unknown"));
            $item->setSupportHosted(trim($doc->find("#marketplace-addon-detail-purchase-more h3:eq(1) + ul li")->text()));
            $item->setPassedAutomatedTests(true);
            $item->setPassedPrbReview(true);
            $item->setLastSync($this->settings->getSyncStartTime());

            $screenshotUrls = array();

            foreach($doc->find(".grouped-elements") as $img) {
                $screenshotUrl = \PhpQuery\PhpQuery::pq($img)->attr("href");

                array_push($screenshotUrls, $screenshotUrl);
            }

            if (is_array($screenshotUrls) && count($screenshotUrls) > 0) {
                foreach($screenshotUrls as $screenshotUrl) {
                    if (is_object($image = $this->em->getRepository('Concrete\Package\AddonList\Src\Entity\MarketplaceAddonImage')->findOneBy(array("slug" => $item->getSlug(), "imageUrl" => $screenshotUrl))) === false) {
                        $image = new MarketplaceAddonImage;
                    }
                    
                    $image->setSlug($item->getSlug());
                    $image->setImageUrl($screenshotUrl);
                    $image->setLastSync($this->settings->getSyncStartTime());

                    // add image entity to database
                    $this->em->persist($image);
                    $this->em->flush();
                }

            } else {
                // warning: no screenshots available for the current item
            }

            // add item entity to database
            $this->em->persist($item);
            $this->em->flush();
            
        } catch (\Exception $e) {
            $this->logout();
            
            // some error happend
            $this->errorList->add($e->getMessage());
            
            $this->raiseError();
        } 
        
        return $this->errorList->has() === false;
    }
    
    /**
     * @param string $marketplaceItemUrl
     * 
     * @return boolean
     */
    public function processMarketplaceItemUrl($marketplaceItemUrl) {
        try {
            $relativePath = substr($marketplaceItemUrl, 25);
            
            $slug = substr($relativePath, 20, strlen($relativePath) - 21);
            
            // init item entity
            if (is_object($item = $this->em->getRepository('Concrete\Package\AddonList\Src\Entity\MarketplaceAddon')->findOneBy(array("slug" => $slug))) === false) {
                $item = new MarketplaceAddon;
            }
            
            if (substr($relativePath, 0, 20) === "/marketplace/addons/") {
                $item->setIsTheme(false);
                $item->setSlug($slug);

                // fetch more informations about the item from details page
                $doc = Helpers::fetchDOM($item->getMarketplacePageUrl());
                
                // determinante marketplace
                if ($doc->find(".ccm-rating")->length === 0) {
                    $this->processModernMarketplaceAddon($item, $doc);
                } else {
                    $this->processLegacyMarketplaceAddon($item, $doc);
                }
                
            } else if (substr($relativePath, 0, 20) === "/marketplace/themes/") {
                $item->setIsTheme(true);
                $item->setSlug($slug);

                // fetch more informations about the item from details page
                $doc = Helpers::fetchDOM($item->getMarketplacePageUrl());
                
                // determinante marketplace
                if ($doc->find(".ccm-rating")->length === 0) {
                    $this->processModernMarketplaceTheme($item, $doc);
                } else {
                    $this->processLegacyMarketplaceTheme($item, $doc);
                }
            }
            
        } catch (\Exception $e) {
            $this->logout();
            
            // some error happend
            $this->errorList->add($e->getMessage());
        } 
        
        return $this->errorList->has() === false;
    }
    
    /**
     * @return array
     */
    public function getMarketplaceItemUrls() {
        $urlSet = array();
        
        // fetch items
        $doc = Helpers::fetchDOM($this->settings->getProfilePageUrl());

        foreach($doc->find(".icon-list a[href^='/marketplace']") as $a) {
            $marketplaceItemUrl = "https://www.concrete5.org" . trim(\PhpQuery\PhpQuery::pq($a)->attr("href"));
            
            array_push($urlSet, $marketplaceItemUrl);
        }
        
        return $urlSet;
    }
    
    /**
     * @return array
     */
    public function getMarketplaceTypes() {
        return array(
            self::MARKETPLACE_TYPE_LEGACY => t("Legacy"),
            self::MARKETPLACE_TYPE_MODERN => t("Modern"),
            self::MARKETPLACE_TYPE_BOTH => t("Both")
        );
    }
    
    /**
     * 
     * @param string $sortColumn
     * @param string $sortOrder
     * @param integer $marketplaceType
     * @param boolean $hasPagination
     * @param integer $currentPage
     * @param integer $itemsPerPage
     * 
     * @return array
     */
    public function getItems($sortColumn = "downloadStats", $sortOrder = "asc", $marketplaceType = self::MARKETPLACE_TYPE_BOTH, $hasPagination = false, $currentPage = 1, $itemsPerPage = 25) {
        switch($sortColumn) {
            case "downloadStats":
                $sortColumn = "addon.downloadStats";
                break;
            
            case "title":
                $sortColumn = "addon.title";
                break;
            
            case "singlePrice":
                $sortColumn = "addon.singlePrice";
                break;
            
            case "starRatingAverage":
                $sortColumn = "addon.starRatingAverage";
                break;
            
            default:
                return;
        }
        
        if (in_array(strtolower($sortOrder), array("asc", "desc")) === false) {
            return;
        }
        
        $queryBuilder = $this->em->createQueryBuilder()
            ->select('addon')
            ->from('Concrete\Package\AddonList\Src\Entity\MarketplaceAddon', 'addon')
            ->orderBy($sortColumn, $sortOrder);
        
        if ($marketplaceType == self::MARKETPLACE_TYPE_LEGACY) {
            $queryBuilder->where("addon.isLegacy = 1");
        } else if ($marketplaceType == self::MARKETPLACE_TYPE_MODERN) {
            $queryBuilder->where("addon.isLegacy = 0");
        } 
        
        if ($hasPagination) {
            $queryBuilder
                ->setFirstResult($itemsPerPage * ($currentPage - 1))
                ->setMaxResults($itemsPerPage);
        } 

        $paginator = new Paginator($queryBuilder);

        $countIfItems = count($paginator);
        
        return array(
            "totalItems" => $countIfItems,
            "results" => $queryBuilder->getQuery()->getResult()
        );
    }
    
    
    /**
     * 
     * @return array
     */
    public function getItemList() {
        $itemList = array();
        
        $items = $this->getItems();
        
        if (is_array($items["results"])) {
            foreach($items["results"] as $item) {
                $itemList[$item->getSlug()] = $item->getTitle();
            }
        }
        
        return $itemList;
    }
    
    /**
     * 
     * @param string $slug
     * 
     * @return MarketplaceAddon
     */
    public function getItemBySlug($slug) {
        return $this->em->getRepository('Concrete\Package\AddonList\Src\Entity\MarketplaceAddon')->findOneBy(array("slug" => $slug));
    }
  
    
    /**
     * 
     * @param string $slug
     * 
     * @return MarketplaceAddon
     */
    public function getEmptyItem() {
        $emptyAddon = new MarketplaceAddon;
        
        $emptyAddon->setTitle(t("Not available"));
        $emptyAddon->setDescription(sprintf("<p>%s</p>", t("No description available.")));
        $emptyAddon->setVersionNumber(t("Unknown"));
        $emptyAddon->setSupportResponse(t("Unknown"));
        $emptyAddon->setSupportHosted(t("Unknown"));
        $emptyAddon->setCompatibleConcreteVersion(t("Unknown"));
        $emptyAddon->setLicenseName(t("Unknown"));
        
        return $emptyAddon;
    }
  
}