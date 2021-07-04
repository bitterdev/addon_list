<?php

/**
 * @project:   Addon list add-on for concrete5
 * 
 * @author     Fabian Bitter
 * @copyright  (C) 2017 Fabian Bitter (www.bitter.de)
 * @version    1.0
 */

namespace Concrete\Package\AddonList\Src\Entity;

defined('C5_EXECUTE') or die('Access denied');

use Concrete\Package\AddonList\Src\MarketplaceSettings;
use Concrete\Package\AddonList\Src\Helpers;
use Database;

/**
 * @Entity
 * @Table(name="MarketplaceAddon")
 * */
class MarketplaceAddon implements \JsonSerializable {

    const SKILL_LEVEL_BEGINNER = 0;
    const SKILL_LEVEL_INTERMEDIATE = 1;
    const SKILL_LEVEL_EXPERT = 2;
    const SKILL_LEVEL_BLEEDING_EDGE = 3;
    
    /**
     * @Id
     * @Column(type="integer")
     * @GeneratedValue(strategy="AUTO")
     */
    protected $id;
    
    /**
     * @Column(type="string")
     */
    protected $slug = '';

    /**
     * @Column(type="string")
     */
    protected $title = '';

    /**
     * @Column(type="string")
     */
    protected $shortDescription = '';

    /**
     * @Column(type="text", length=65535)
     */
    protected $description = '';

    /**
     * @Column(type="string")
     */
    protected $iconUrl = '';

    /**
     * @Column(type="string")
     */
    protected $versionNumber = '';

    /**
     * @Column(type="decimal", precision=15, scale=2)
     */
    protected $singlePrice = 0;

    /**
     * @Column(type="decimal", precision=15, scale=2)
     */
    protected $packagePrice = 0;

    /**
     * @Column(type="string", length=2048)
     */
    protected $videoEmbedCode = '';

    /**
     * @Column(type="string")
     */
    protected $liveDemoUrl = '';

    /**
     * @Column(type="boolean")
     */
    protected $fullyTranslatable = false;

    /**
     * @Column(type="boolean")
     */
    protected $needsExternalLibraries = false;

    /**
     * @Column(type="string")
     */
    protected $compatibleConcreteVersion = '';

    /**
     * @Column(type="string")
     */
    protected $licenseName = '';

    /**
     * @Column(type="string")
     */
    protected $licenseUrl = '';

    /**
     * @Column(type="string")
     */
    protected $supportResponse = '';

    /**
     * @Column(type="string")
     */
    protected $supportHosted = '';

    /**
     * @Column(type="boolean")
     */
    protected $needsExtraServerPermissions = false;

    /**
     * @Column(type="boolean")
     */
    protected $needsInternet = false;

    /**
     * @Column(type="boolean")
     */
    protected $passedAutomatedTests = false;

    /**
     * @Column(type="boolean")
     */
    protected $passedPrbReview = false;

    /**
     * @Column(type="decimal", precision=3, scale=2)
     */
    protected $starRatingAverage = 0;

    /**
     * @Column(type="integer")
     */
    protected $starRatingCount = 0;

    /**
     * @Column(type="string")
     */
    protected $profileUrl = '';

    /**
     * @Column(type="string")
     */
    protected $showcaseUrl = '';

    /**
     * @Column(type="integer")
     */
    protected $skillLevel = 0;

    /**
     * @Column(type="boolean")
     */
    protected $isLegacy = false;

    /**
     * @Column(type="integer")
     */
    protected $singleSKU = 0;

    /**
     * @Column(type="integer")
     */
    protected $packageSKU = 0;

    /**
     * @Column(type="integer")
     */
    protected $packageDiscount = 0;

    /**
     * @Column(type="string")
     */
    protected $quoteReview = '';

    /**
     * @Column(type="string")
     */
    protected $quoteAuthor = '';

    /**
     * @Column(type="string")
     */
    protected $quoteLink = '';

    /**
     * @Column(type="boolean")
     */
    protected $isTheme = false;

    /**
     * @Column(type="boolean")
     */
    protected $hasCustomizableStyles = false;

    /**
     * @Column(type="string")
     */
    protected $pageTypes = '';

    /**
     * @Column(type="string")
     */
    protected $mainThemeImage = '';

    /**
     * @Column(type="integer")
     */
    protected $lastSync = 0;

    /**
     * @Column(type="integer")
     */
    protected $downloadStats = 0;
    
    
    public function getIsTheme() {
        return $this->isTheme;
    }

    public function getHasCustomizableStyles() {
        return $this->hasCustomizableStyles;
    }

    public function getPageTypes() {
        return $this->pageTypes;
    }

    public function getMainThemeImage() {
        return $this->mainThemeImage;
    }

    public function setIsTheme($isTheme) {
        $this->isTheme = $isTheme;
    }

    public function setHasCustomizableStyles($hasCustomizableStyles) {
        $this->hasCustomizableStyles = $hasCustomizableStyles;
    }

    public function setPageTypes($pageTypes) {
        $this->pageTypes = $pageTypes;
    }

    public function setMainThemeImage($mainThemeImage) {
        $this->mainThemeImage = $mainThemeImage;
    }

    public function hasPackageDiscount() {
        return $this->getPackageDiscount() > 0;
    }
    
    public function hasQuote() {
        return strlen($this->getQuoteReview()) > 0;
    }
    
    public function getQuoteReview() {
        return $this->quoteReview;
    }

    public function getQuoteAuthor() {
        return $this->quoteAuthor;
    }

    public function setQuoteReview($quoteReview) {
        $this->quoteReview = $quoteReview;
    }

    public function setQuoteAuthor($quoteAuthor) {
        $this->quoteAuthor = $quoteAuthor;
    }
    
    public function getQuoteLink() {
        return $this->quoteLink;
    }

    public function setQuoteLink($quoteLink) {
        $this->quoteLink = $quoteLink;
    }

    public function getPackageDiscount() {
        return $this->packageDiscount;
    }

    public function setPackageDiscount($packageDiscount) {
        $this->packageDiscount = $packageDiscount;
    }

    public function getSingleSKU() {
        return $this->singleSKU;
    }

    public function getPackageSKU() {
        return $this->packageSKU;
    }

    public function setSingleSKU($singleSKU) {
        $this->singleSKU = $singleSKU;
    }

    public function setPackageSKU($packageSKU) {
        $this->packageSKU = $packageSKU;
    }

    public function getIsLegacy() {
        return $this->isLegacy;
    }

    public function setIsLegacy($isLegacy) {
        $this->isLegacy = $isLegacy;
    }

    public function getShowcaseUrl() {
        return $this->showcaseUrl;
    }

    public function setShowcaseUrl($showcaseUrl) {
        $this->showcaseUrl = $showcaseUrl;
    }

    public function getTitle() {
        return $this->title;
    }

    public function getSlug() {
        return $this->slug;
    }

    public function getShortDescription() {
        return $this->shortDescription;
    }

    public function getDescription() {
        return $this->description;
    }

    public function getIconUrl() {
        if (strlen($this->iconUrl) > 0) {
            return $this->iconUrl;
        } else {
            return $this->getEmptyIconUrl();
        }
    }

    public function getVersionNumber() {
        return $this->versionNumber;
    }

    public function setTitle($title) {
        $this->title = $title;
    }

    public function setSlug($slug) {
        $this->slug = $slug;
    }

    public function setShortDescription($shortDescription) {
        $this->shortDescription = $shortDescription;
    }

    public function setDescription($description) {
        $this->description = $description;
    }

    public function setIconUrl($iconUrl) {
        $this->iconUrl = $iconUrl;
    }

    public function setVersionNumber($versionNumber) {
        $this->versionNumber = $versionNumber;
    }

    public function getSinglePrice() {
        return $this->singlePrice;
    }

    public function getPackagePrice() {
        return $this->packagePrice;
    }

    public function setSinglePrice($singlePrice) {
        $this->singlePrice = $singlePrice;
    }

    public function setPackagePrice($packagePrice) {
        $this->packagePrice = $packagePrice;
    }

    public function getLastSync() {
        return $this->lastSync;
    }

    public function setLastSync($lastSync) {
        $this->lastSync = $lastSync;
    }
    
    public function hasPackageSKU() {
        return $this->getPackageSKU() > 0;
    }

    public function getBuySingleItemLinkUrl() {
        return sprintf("https://www.concrete5.org/cart/-/add/%s/", $this->getSingleSKU());
    }

    public function getBuyPackageItemLinkUrl() {
        return sprintf("https://www.concrete5.org/cart/-/add/%s/", $this->getPackageSKU());
    }

    public function getMarketplacePageUrl() {
        if ($this->getIsTheme()) {
            return sprintf("https://www.concrete5.org/marketplace/themes/%s/", $this->getSlug());
        } else {
            return sprintf("https://www.concrete5.org/marketplace/addons/%s/", $this->getSlug());
        }
    }

    public function getMarketplaceReviewUrl() {
        if ($this->getIsTheme()) {
            return sprintf("https://www.concrete5.org/marketplace/themes/%s/reviews", $this->getSlug());
        } else {
            return sprintf("https://www.concrete5.org/marketplace/addons/%s/reviews", $this->getSlug());
        }
    }

    public function getLiveDemoUrl() {
        return $this->liveDemoUrl;
    }

    public function hasLiveDemoUrl() {
        return strlen($this->getLiveDemoUrl()) > 0 && $this->getLiveDemoUrl() != "#";
    }

    public function getFullyTranslatable() {
        return $this->fullyTranslatable;
    }

    public function getNeedsExternalLibraries() {
        return $this->needsExternalLibraries;
    }

    public function getCompatibleConcreteVersion() {
        return $this->compatibleConcreteVersion;
    }

    public function getLicenseName() {
        return $this->licenseName;
    }

    public function getLicenseUrl() {
        return $this->licenseUrl;
    }

    public function getSupportResponse() {
        return $this->supportResponse;
    }

    public function getSupportHosted() {
        return $this->supportHosted;
    }

    public function getNeedsExtraServerPermissions() {
        return $this->needsExtraServerPermissions;
    }

    public function getNeedsInternet() {
        return $this->needsInternet;
    }

    public function getPassedAutomatedTests() {
        return $this->passedAutomatedTests;
    }

    public function getPassedPrbReview() {
        return $this->passedPrbReview;
    }

    public function setLiveDemoUrl($liveDemoUrl) {
        $this->liveDemoUrl = $liveDemoUrl;
    }

    public function setFullyTranslatable($fullyTranslatable) {
        $this->fullyTranslatable = $fullyTranslatable;
    }

    public function setNeedsExternalLibraries($needsExternalLibraries) {
        $this->needsExternalLibraries = $needsExternalLibraries;
    }

    public function setCompatibleConcreteVersion($compatibleConcreteVersion) {
        $this->compatibleConcreteVersion = $compatibleConcreteVersion;
    }

    public function setLicenseName($licenseName) {
        $this->licenseName = $licenseName;
    }

    public function setLicenseUrl($licenseUrl) {
        $this->licenseUrl = $licenseUrl;
    }

    public function setSupportResponse($supportResponse) {
        $this->supportResponse = $supportResponse;
    }

    public function setSupportHosted($supportHosted) {
        $this->supportHosted = $supportHosted;
    }

    public function setNeedsExtraServerPermissions($needsExtraServerPermissions) {
        $this->needsExtraServerPermissions = $needsExtraServerPermissions;
    }

    public function setNeedsInternet($needsInternet) {
        $this->needsInternet = $needsInternet;
    }

    public function setPassedAutomatedTests($passedAutomatedTests) {
        $this->passedAutomatedTests = $passedAutomatedTests;
    }

    public function setPassedPrbReview($passedPrbReview) {
        $this->passedPrbReview = $passedPrbReview;
    }

    public function getStarRatingAverage() {
        return $this->starRatingAverage;
    }

    public function getStarRatingCount() {
        return $this->starRatingCount;
    }

    public function getProfileUrl() {
        return $this->profileUrl;
    }

    public function getProfileName() {
        return MarketplaceSettings::getInstance()->getUsername();
    }

    public function getSkillLevel() {
        return $this->skillLevel;
    }

    public function setStarRatingAverage($starRatingAverage) {
        $this->starRatingAverage = $starRatingAverage;
    }

    public function setStarRatingCount($starRatingCount) {
        $this->starRatingCount = $starRatingCount;
    }

    public function setProfileUrl($profileUrl) {
        $this->profileUrl = $profileUrl;
    }

    public function setSkillLevel($skillLevel) {
        $this->skillLevel = $skillLevel;
    }
    
    public function getVideoEmbedCode() {
        return $this->videoEmbedCode;
    }
    
    public function hasVideoEmbedCode() {
        return strlen($this->getVideoEmbedCode()) > 0;
    }

    public function setVideoEmbedCode($videoEmbedCode) {
        $this->videoEmbedCode = $videoEmbedCode;
    }
    
    /**
     * @return string
     */
    public function getSkillLevelImageUrl() {
        switch($this->getSkillLevel()) {
            case self::SKILL_LEVEL_BEGINNER:
                return "https://www.concrete5.org/themes/version_3/images/marketplace_badge_safety_beginner.png";
                
            case self::SKILL_LEVEL_INTERMEDIATE:
                return "https://www.concrete5.org/themes/version_3/images/marketplace_badge_safety_intermediate.png";
                
            case self::SKILL_LEVEL_EXPERT:
                return "https://www.concrete5.org/themes/version_3/images/marketplace_badge_safety_expert.png";
                
            case self::SKILL_LEVEL_BLEEDING_EDGE:
                return "https://www.concrete5.org/themes/version_3/images/marketplace_badge_safety_bleeding_edge.png";
        }
    }
    
    /**
     * @return string
     */
    public function getSkillLevelName() {
        switch($this->getSkillLevel()) {
            case self::SKILL_LEVEL_BEGINNER:
                return t("Beginner");
                
            case self::SKILL_LEVEL_INTERMEDIATE:
                return t("Intermediate");
                
            case self::SKILL_LEVEL_EXPERT:
                return t("Expert");
                
            case self::SKILL_LEVEL_BLEEDING_EDGE:
                return t("Bleeding Edge");
        }
    }
    
    /**
     * @return string
     */
    public function getSkillLevelCssClass() {
        switch($this->getSkillLevel()) {
            case self::SKILL_LEVEL_BEGINNER:
                return "beginner";
                
            case self::SKILL_LEVEL_INTERMEDIATE:
                return "intermediate";
                
            case self::SKILL_LEVEL_EXPERT:
                return "expert";
                
            case self::SKILL_LEVEL_BLEEDING_EDGE:
                return "bleeding-edge";
        }
    }
    
    /**
     * @return array
     */
    public function getScreenshots() {
        return Database::connection()
            ->getEntityManager()
            ->getRepository('Concrete\Package\AddonList\Src\Entity\MarketplaceAddonImage')
            ->findBy(
                array(
                    "slug" => $this->getSlug()
                )
            );
    }
    
    /**
     * @return boolean
     */
    public function hasScreenshots() {
        return is_array($this->getScreenshots()) && count($this->getScreenshots()) > 0;
    }
    
    /**
     * @return array
     */
    public function getScreenshotList() {
        $screenshots = array();
        
        if ($this->hasScreenshots()) {
            foreach($this->getScreenshots() as $screenshotEntity) {
                $screenshot = $screenshotEntity->getImageUrl();
                
                array_push($screenshots, $screenshot);
            }
        }
        
        return $screenshots;
    }
    
    public function getDownloadStats() {
        return $this->downloadStats;
    }

    public function setDownloadStats($downloadStats) {
        $this->downloadStats = $downloadStats;
    }
    
    /**
     * @return string
     */
    public function getFirstScreenshotUrl() {
        if ($this->getIsTheme()) {
            return $this->getMainThemeImage();
        } else {
            if ($this->hasScreenshots()) {
                $firstImage = $this->getScreenshots()[0];

                return $firstImage->getImageUrl();
            } else {
                return $this->getEmptyScreenshotUrl();
            }
        }
    }
    
    /**
     * @return string
     */
    public function getEmptyScreenshotUrl() {
        return Helpers::getImageUrl("default-thumbnail.jpg");
    }
    
    /**
     * @return string
     */
    public function getEmptyIconUrl() {
        return Helpers::getImageUrl("default-icon.png");
    }

    public function jsonSerialize() {
        return [
            "name" => $this->getTitle(),
            "description" => $this->getShortDescription(),
            "url" => $this->getMarketplacePageUrl(),
            "icon" => $this->getIconUrl()
        ];
    }
}
