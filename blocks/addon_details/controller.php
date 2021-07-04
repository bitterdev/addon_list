<?php

/**
 * @project:   Addon list add-on for concrete5
 *
 * @author     Fabian Bitter
 * @copyright  (C) 2017 Fabian Bitter (www.bitter.de)
 * @version    1.0
 */

namespace Concrete\Package\AddonList\Block\AddonDetails;

defined("C5_EXECUTE") or die("Access Denied.");

use Concrete\Package\AddonList\Src\MarketplaceAddons;
use Concrete\Package\AddonList\Src\Helpers;
use Concrete\Core\Block\BlockController;
use Core;

class Controller extends BlockController {

    public $helpers = array(
        'form'
    );

    public $btFieldsRequired = array(
        'slug',
        'iconVisible',
        'descriptionVisible',
        'descriptionColor',
        'titleVisible',
        'titleColor',
        'subtitleColor',
        'specificationsVisible',
        'specificationsBackgroundColor',
        'specificationsBorderColor',
        'specificationsTextColor',
        'specificationsValueColor',
        'specificationsCheckedColor',
        'specificationsSlug',
        'singleButtonVisible',
        'singleButtonTextColor',
        'singleButtonBackgroundColor',
        'packageButtonVisible',
        'packageButtonTextColor',
        'packageButtonBackgroundColor',
        'normalStarColor',
        'activeStarColor',
        'reviewTextColor',
        'skillLevelVisible',
        'galleryVisible',
        'galleryBackgroundColor',
        'galleryBorderColor',
        'galleryFooterBackgroundColor',
        'galleryFooterLinkNormalColor',
        'galleryFooterLinkHoverColor',
        'galleryFooterLinkDisabledColor',
        'quoteVisible',
        'quoteTextColor',
        'quoteAuthorLinkColor',
        'packageButtonDiscountColor'
    );

    protected $btExportFileColumns = array();
    protected $btTable = 'btAddonItem';
    protected $btInterfaceWidth = 400;
    protected $btInterfaceHeight = 500;
    protected $btCacheBlockRecord = true;
    protected $btCacheBlockOutput = true;
    protected $btCacheBlockOutputLifetime = 300;
    protected $btCacheBlockOutputOnPost = true;
    protected $btCacheBlockOutputForRegisteredUsers = true;

    public function getBlockTypeDefaultSet() {
            return 'developer';
    }

    public function getBlockTypeDescription() {
        return t('Displays details about an add-on.');
    }

    public function getBlockTypeName() {
        return t("Add-on details");
    }

    private function addOrEdit() {
        $items = MarketplaceAddons::getInstance()->getItemList();

        array_unshift($items, t("(Automatically detect)"));

        $this->set("items", $items);
    }

    public function add() {
        $this->addOrEdit();

        $this->set("iconVisible", true);
        $this->set("titleVisible", true);
        $this->set("titleColor", '#333333');
        $this->set("subtitleColor", '#a4a4a4');
        $this->set("ratingVisible", true);
        $this->set("normalStarColor", '#666666');
        $this->set("activeStarColor", '#0099ff');
        $this->set("reviewTextColor", '#666666');
        $this->set("skillLevelVisible", true);
        $this->set("singleButtonVisible", true);
        $this->set("singleButtonTextColor", '#ffffff');
        $this->set("singleButtonBackgroundColor", '#007ccf');
        $this->set("packageButtonVisible", true);
        $this->set("packageButtonTextColor", '#ffffff');
        $this->set("packageButtonBackgroundColor", '#4c4c4c');
        $this->set("descriptionVisible", true);
        $this->set("descriptionColor", '#333333');
        $this->set("specificationsVisible", true);
        $this->set("specificationsBackgroundColor", '#e8f8ff');
        $this->set("specificationsBorderColor", '#99ccff');
        $this->set("specificationsTextColor", '#222222');
        $this->set("specificationsValueColor", '#0099ff');
        $this->set("specificationsCheckedColor", '#468847');
        $this->set("galleryVisible", true);
        $this->set("galleryBackgroundColor", '#f8f8f8');
        $this->set("galleryBorderColor", '#a8a8a8');
        $this->set("galleryFooterBackgroundColor", '#ffffff');
        $this->set("galleryFooterLinkNormalColor", '#999999');
        $this->set("galleryFooterLinkHoverColor", '#0099ff');
        $this->set("galleryFooterLinkDisabledColor", '#cccccc');
        $this->set("quoteVisible", true);
        $this->set("quoteTextColor", '#666666');
        $this->set("quoteAuthorLinkColor", '#0086ca');
        $this->set("packageButtonDiscountColor", '#ffa800');
    }

    public function edit() {
        $this->addOrEdit();
    }

    public function view() {
        $this->requireAsset("css", "fontawesome");

        if ($this->galleryVisible) {
            // load lightbox2 assets when gallery is visible
            $this->requireAsset("fancybox-plus");
        }

        $this->set("item", $this->getActiveItem());
    }

    private function getActiveItem() {
        $item = MarketplaceAddons::getInstance()->getItemBySlug($this->slug);

        if (is_object($item) === false) {
            $item = MarketplaceAddons::getInstance()->getItemBySlug($this->request->get("addon_item"));
        }

        if (is_object($item) === false) {
            $item = MarketplaceAddons::getInstance()->getEmptyItem();
        }

        return $item;
    }

    public function getSearchableContent() {
        $item = $this->getActiveItem();

        $content = strip_tags($item->getDescription());

        return $content;
    }

    public function validate($args) {
        $e = Core::make('helper/validation/error');

        $arrCheckColors = array(
            'descriptionColor' => array(
                "sectionName" => t('Description'),
                "fieldName" => t('Text')
            ),

            'titleColor' => array(
                "sectionName" => t('Title'),
                "fieldName" => t('Title')
            ),

            'subtitleColor' => array(
                "sectionName" => t('Title'),
                "fieldName" => t('Subtitle')
            ),

            'specificationsBackgroundColor' => array(
                "sectionName" => t('Specifications'),
                "fieldName" => t('Background')
            ),

            'specificationsBorderColor' => array(
                "sectionName" => t('Specifications'),
                "fieldName" => t('Border')
            ),

            'specificationsTextColor' => array(
                "sectionName" => t('Specifications'),
                "fieldName" => t('Text')
            ),

            'specificationsValueColor' => array(
                "sectionName" => t('Specifications'),
                "fieldName" => t('Value')
            ),

            'specificationsCheckedColor' => array(
                "sectionName" => t('Specifications'),
                "fieldName" => t('Tests')
            ),

            'singleButtonTextColor' => array(
                "sectionName" => t('Buybutton (single)'),
                "fieldName" => t('Text')
            ),

            'singleButtonBackgroundColor' => array(
                "sectionName" => t('Buybutton (single)'),
                "fieldName" => t('Background')
            ),

            'packageButtonTextColor' => array(
                "sectionName" => t('Buybutton (package)'),
                "fieldName" => t('Text')
            ),

            'packageButtonBackgroundColor' => array(
                "sectionName" => t('Buybutton (package)'),
                "fieldName" => t('Background')
            ),

            'packageButtonDiscountColor' => array(
                "sectionName" => t('Buybutton (package)'),
                "fieldName" => t('Discount')
            ),

            'normalStarColor' => array(
                "sectionName" => t('Rating'),
                "fieldName" => t('Star (normal)')
            ),

            'activeStarColor' => array(
                "sectionName" => t('Rating'),
                "fieldName" => t('Star (active)')
            ),

            'reviewTextColor' => array(
                "sectionName" => t('Rating'),
                "fieldName" => t('Text')
            ),

            'galleryBackgroundColor' => array(
                "sectionName" => t('Gallery'),
                "fieldName" => t('Background')
            ),

            'galleryBorderColor' => array(
                "sectionName" => t('Gallery'),
                "fieldName" => t('Border')
            ),

            'galleryFooterBackgroundColor' => array(
                "sectionName" => t('Gallery'),
                "fieldName" => t('Footer background')
            ),

            'galleryFooterLinkNormalColor' => array(
                "sectionName" => t('Footer'),
                "fieldName" => t('Footer link (normal)')
            ),

            'galleryFooterLinkHoverColor' => array(
                "sectionName" => t('Footer'),
                "fieldName" => t('Footer link (hover)')
            ),

            'galleryFooterLinkDisabledColor' => array(
                "sectionName" => t('Footer'),
                "fieldName" => t('Footer link (disabled)')
            ),

            'quoteTextColor' => array(
                "sectionName" => t('Quote'),
                "fieldName" => t('Text')
            ),

            'quoteAuthorLinkColor' => array(
                "sectionName" => t('Quote'),
                "fieldName" => t('Author')
            )
        );

        foreach($arrCheckColors as $varName => $varDetails) {
            if (Helpers::isValidColor($args[$varName]) === false) {
                $e->add(t("You must specify a valid color for the field \"%s\" in the section \"%s\".", $varDetails["fieldName"], $varDetails["sectionName"]));
            }
        }

        return $e;
    }
}
