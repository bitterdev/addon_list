<?php

/**
 * @project:   Addon list add-on for concrete5
 *
 * @author     Fabian Bitter
 * @copyright  (C) 2017 Fabian Bitter (www.bitter.de)
 * @version    1.0
 */

namespace Concrete\Package\AddonList\Block\AddonList;

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
        'backgroundColor',
        'borderColor',
        'footerColor',
        'priceColor',
        'textColor',
        'sortColumn',
        'sortOrder',
        'normalStarColor',
        'activeStarColor',
        'reviewTextColor',
        'productPageId',
        'marketplaceType',
        'paginationItemBackgroundColor',
        'paginationItemTextColor',
        'paginationItemBorderColor',
        'paginationItemActiveTextColor',
        'paginationItemActiveBackgroundColor',
        'hasPagination',
        'itemsPerPage'
    );

    protected $btExportFileColumns = array();
    protected $btTable = 'btAddonList';
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
        return t('Lists all available add-ons.');
    }

    public function getBlockTypeName() {
        return t("Add-on list");
    }

    public function edit() {
        $this->set("marketplaceTypes", MarketplaceAddons::getInstance()->getMarketplaceTypes());
    }

    public function add() {
        $this->set("sortColumn", "title");
        $this->set("sortOrder", "asc");
        $this->set("backgroundColor", "#ffffff");
        $this->set("borderColor", "#e5e5e5");
        $this->set("textColor", "#000000");
        $this->set("priceColor", "#0099ff");
        $this->set("footerColor", "#828282");
        $this->set("normalStarColor", "#666666");
        $this->set("activeStarColor", "#0099ff");
        $this->set("reviewTextColor", "#666666");
        $this->set("marketplaceType", MarketplaceAddons::MARKETPLACE_TYPE_BOTH);
        $this->set("marketplaceTypes", MarketplaceAddons::getInstance()->getMarketplaceTypes());
        $this->set("paginationItemBackgroundColor", "#ffffff");
        $this->set("paginationItemTextColor", "#0184c5");
        $this->set("paginationItemBorderColor", "#dddddd");
        $this->set("paginationItemActiveTextColor", "#999999");
        $this->set("paginationItemActiveBackgroundColor", "#f5f5f5");
        $this->set("hasPagination", "0");
        $this->set("itemsPerPage", "25");
    }

    public function view() {
        $this->requireAsset("css", "fontawesome");

        $items = MarketplaceAddons::getInstance()->getItems($this->sortColumn, $this->sortOrder, $this->marketplaceType, intval($this->hasPagination) === 1, $this->getCurrentPage(), $this->itemsPerPage);

        $this->set("currentPage", $this->getCurrentPage());
        $this->set("items", $items["results"]);
        $this->set("countOfItems", $items["totalItems"]);
    }

    private function getCurrentPage() {
        $currentPage = intval($this->request->get("addon_page"));

        if ($currentPage === 0) {
            $currentPage = 1;
        }

        return $currentPage;
    }

    public function getSearchableContent() {
        $content = "";

        $items = MarketplaceAddons::getInstance()->getItems($this->sortColumn, $this->sortOrder, $this->marketplaceType);

        if (is_array($items["results"])) {
            foreach($items["results"] as $item) {
                $content .= sprintf(
                    "%s %s",
                    $item->getTitle(),
                    $item->getShortDescription()
                );
            }
        }

        return $content;
    }

    public function validate($args) {
        $e = Core::make('helper/validation/error');

        $arrCheckColors = array(
            'backgroundColor' => array(
                "sectionName" => t('General'),
                "fieldName" => t('Background')
            ),

            'borderColor' => array(
                "sectionName" => t('General'),
                "fieldName" => t('Border')
            ),

            'footerColor' => array(
                "sectionName" => t('General'),
                "fieldName" => t('Footer')
            ),

            'priceColor' => array(
                "sectionName" => t('General'),
                "fieldName" => t('Price')
            ),

            'textColor' => array(
                "sectionName" => t('General'),
                "fieldName" => t('Text')
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

            'paginationItemBackgroundColor' => array(
                "sectionName" => t('Pagination'),
                "fieldName" => t('Background')
            ),

            'paginationItemTextColor' => array(
                "sectionName" => t('Pagination'),
                "fieldName" => t('Text')
            ),

            'paginationItemBorderColor' => array(
                "sectionName" => t('Pagination'),
                "fieldName" => t('Border')
            ),

            'paginationItemActiveTextColor' => array(
                "sectionName" => t('Pagination'),
                "fieldName" => t('Text (active)')
            ),

            'paginationItemActiveBackgroundColor' => array(
                "sectionName" => t('Pagination'),
                "fieldName" => t('Background (active)')
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
