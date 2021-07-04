<?php

/**
 * @project:   Addon list add-on for concrete5
 *
 * @author     Fabian Bitter
 * @copyright  (C) 2017 Fabian Bitter (www.bitter.de)
 * @version    1.0
 */

namespace Concrete\Package\AddonList;

use Concrete\Core\Http\ResponseFactory;
use Concrete\Core\Support\Facade\Application;
use Doctrine\ORM\EntityManager;
use PhpQuery\PhpQuery as phpQuery;
use Concrete\Package\AddonList\Src\MarketplaceSettings;
use Concrete\Package\AddonList\Src\MarketplaceAddons;
use Concrete\Core\Block\BlockType\Set;
use Concrete\Core\Job\Job as Job;
use Package;
use BlockType;
use SinglePage;
use Request;
use Database;
use Page;
use PageType;
use AssetList;
use Core;
use Route;

class Controller extends Package {
    protected $pkgHandle = 'addon_list';
    protected $pkgVersion = '1.1.2';
    protected $appVersionRequired = '5.7.0.4';

    public function getPackageDescription() {
        return t('Represent your concrete5 add-ons on your website.');
    }

    public function getPackageName() {
        return t('Add-on list');
    }

    public function on_start() {
        $this->initComponents();
        $this->addReminderRoute();
    }

    private function addReminderRoute() {
        Route::register("/bitter/" . $this->pkgHandle . "/reminder/hide", function() {
            $this->getConfig()->save('reminder.hide', true);
            $app = Application::getFacadeApplication();
            $app->shutdown();
        });

        Route::register("/bitter/" . $this->pkgHandle . "/did_you_know/hide", function() {
            $this->getConfig()->save('did_you_know.hide', true);
            $app = \Concrete\Core\Support\Facade\Application::getFacadeApplication();
            /** @var $responseFactory \Concrete\Core\Http\ResponseFactory */
            $responseFactory = $app->make(\Concrete\Core\Http\ResponseFactory::class);
            $responseFactory->create("", \Concrete\Core\Http\Response::HTTP_OK)->send();
            $app->shutdown();
        });

        Route::register("/bitter/" . $this->pkgHandle . "/license_check/hide", function() {
            $this->getConfig()->save('license_check.hide', true);
            $app = \Concrete\Core\Support\Facade\Application::getFacadeApplication();
            /** @var $responseFactory \Concrete\Core\Http\ResponseFactory */
            $responseFactory = $app->make(\Concrete\Core\Http\ResponseFactory::class);
            $responseFactory->create("", \Concrete\Core\Http\Response::HTTP_OK)->send();
            $app->shutdown();
        });
    }

    public function initComponents() {
        $this->loadComposerDependencies();
        $this->initPhpQuery();
        $this->registerAssets();
        $this->bindCoreClasses();
        $this->addNewsfeedRoute();
    }

    private function addNewsfeedRoute() {
        Route::register("/bitter/" . $this->pkgHandle . "/feed/json", function() {

            $app = Application::getFacadeApplication();
            /** @var $em EntityManager */
            $em = $app->make(EntityManager::class);
            $enitites = $em->getRepository(\Concrete\Package\AddonList\Src\Entity\MarketplaceAddon::class)->findAll();

            /** @var $responseFactory ResponseFactory */
            $responseFactory = $app->make(ResponseFactory::class);
            $responseFactory->json($enitites)->send();
            $app->shutdown();
        });
    }

    private function bindCoreClasses() {
        Core::bind('Paginator', function() {
            return new \JasonGrimes\Paginator(100, 25, 1, '');
        });
    }

    private function registerAssets() {
        AssetList::getInstance()->register('javascript', 'fancybox-plus', "bower_components/fancybox-plus/dist/jquery.fancybox-plus.min.js", array("version" => "1.3.5"), $this->pkgHandle);
        AssetList::getInstance()->register('javascript', 'jquery/easing', "bower_components/jquery.easing/js/jquery.easing.min.js", array("version" => "1.3.0"), $this->pkgHandle);
        AssetList::getInstance()->register('css', 'fancybox-plus', "bower_components/fancybox-plus/css/jquery.fancybox-plus.css", array("version" => "1.3.5"), $this->pkgHandle);

        AssetList::getInstance()->registerGroup(
            'fancybox-plus',

            array(
                array('css', 'fancybox-plus'),
                array('javascript', 'jquery'),
                array('javascript', 'jquery/easing'),
                array('javascript', 'fancybox-plus')
            )
        );
    }

    private function loadComposerDependencies() {
        if (file_exists($this->getPackagePath() . '/vendor/autoload.php')) {
            require $this->getPackagePath() . '/vendor/autoload.php';
        }
    }

    private function initPhpQuery() {
        // register pq globally
        //phpQuery::use_function();
    }

    private function installOrUpdateJob($jobName) {
        $pkg = Package::getByHandle($this->pkgHandle);

        $jobObject = Job::getByHandle($jobName);

        if (!is_object($jobObject)) {
            $jobObject = Job::installByPackage($jobName, $pkg);
        }
    }

    public function addBlockTypeIfNotExists($blockTypeName) {
        $pkg = Package::getByHandle($this->pkgHandle);

        $blockType = BlockType::getByHandle($blockTypeName);

        if (is_object($blockType) === false) {
            $blockType = BlockType::installBlockType($blockTypeName, $pkg);
        }
    }

    /**
     *
     * @param type $pathToCheck
     * @return boolean
     *
     */
    private function pageExists($pathToCheck) {
        $pkg = Package::getByHandle($this->pkgHandle);

        $pages = SinglePage::getListByPackage($pkg);

        foreach ($pages as $page) {
            if ($page->getCollectionPath() === $pathToCheck) {
                return true;
            }
        }

        return false;
    }

    private function addPageIfNotExists($path, $name, $excludeNav = false) {
        $pkg = Package::getByHandle($this->pkgHandle);

        if ($this->pageExists($path) === false) {
            $singlePage = SinglePage::add($path, $pkg);

            if ($singlePage) {
                $singlePage->update(
                        array(
                            'cName' => $name
                        )
                );

                if ($excludeNav) {
                    $singlePage->setAttribute('exclude_nav', 1);
                }
            }
        }
    }

    public function installOrUpdateBlockTypes() {
        $this->addBlockTypeIfNotExists("addon_list");
        $this->addBlockTypeIfNotExists("addon_details");
    }

    private function installOrUpdateJobs() {
        $this->installOrUpdateJob("sync_addons");
    }

    private function installOrUpdatesPages() {
        $this->addPageIfNotExists("/dashboard/addon_list", t("Add-on list"));
    }

    private function installOrUpdateDemoPages() {
        // add marketplace item detail page if not exists
        if (MarketplaceSettings::getInstance()->getDemoContentInstalled() === false) {
            $parentPage = Page::getById(HOME_CID);

            $detailPage = $parentPage->add(
                PageType::getByHandle('page'),

                array(
                    'cName' => t('Add-on details')
                )
            );

            $detailPage->setAttribute('exclude_nav', true);

            $detailPage->addBlock(
                BlockType::getByHandle('addon_details'),

                'Main',

                array(
                    'slug' => 0,
                    'iconVisible' => 1,
                    'descriptionVisible' => 1,
                    'descriptionColor' => '#333333',
                    'titleVisible' => 1,
                    'titleColor' => '#333333',
                    'subtitleColor' => '#a4a4a4',
                    'specificationsVisible' => 1,
                    'specificationsBackgroundColor' => '#e8f8ff',
                    'specificationsBorderColor' => '#99ccff',
                    'specificationsTextColor' => '#222222',
                    'specificationsValueColor' => '#0099ff',
                    'specificationsCheckedColor' => '#468847',
                    'specificationsSlug',
                    'singleButtonVisible' => 1,
                    'singleButtonTextColor' => '#ffffff',
                    'singleButtonBackgroundColor' => '#007ccf',
                    'packageButtonVisible' => 1,
                    'packageButtonTextColor' => '#ffffff',
                    'packageButtonBackgroundColor' => '#4c4c4c',
                    'normalStarColor' => '#666666',
                    'activeStarColor' => '#0099ff',
                    'reviewTextColor' => '#666666',
                    'skillLevelVisible' => 1,
                    'galleryVisible' => 1,
                    'galleryBackgroundColor' => '#f8f8f8',
                    'galleryBorderColor' => '#a8a8a8',
                    'galleryFooterBackgroundColor' => '#ffffff',
                    'galleryFooterLinkNormalColor' => '#999999',
                    'galleryFooterLinkHoverColor' => '#0099ff',
                    'galleryFooterLinkDisabledColor' => '#cccccc',
                    'quoteVisible' => 1,
                    'quoteTextColor' => '#666666',
                    'quoteAuthorLinkColor' => '#0086ca',
                    'packageButtonDiscountColor' => '#ffa800'
                )
            );

            $overviewPage = $parentPage->add(
                PageType::getByHandle('page'),

                array(
                    'cName' => t('My Add-ons')
                )
            );

            $overviewPage->addBlock(
                BlockType::getByHandle('addon_list'),

                'Main',

                array(
                    'backgroundColor' => '#ffffff',
                    'borderColor' => '#e5e5e5',
                    'footerColor' => '#828282',
                    'priceColor' => '#0099ff',
                    'textColor' => '#000000',
                    'sortColumn' => 'singlePrice',
                    'sortOrder' => 'asc',
                    'normalStarColor' => '#666666',
                    'activeStarColor' => '#0099ff',
                    'reviewTextColor' => '#666666',
                    'paginationItemBackgroundColor' => '#ffffff',
                    'paginationItemTextColor' => '#0184c5',
                    'paginationItemBorderColor' => '#dddddd',
                    'paginationItemActiveTextColor' => '#999999',
                    'paginationItemActiveBackgroundColor' => '#f5f5f5',
                    'hasPagination' => '1',
                    'itemsPerPage' => '25',
                    'productPageId' => $detailPage->getCollectionID(),
                    'marketplaceType' => MarketplaceAddons::MARKETPLACE_TYPE_BOTH
                )
            );

            MarketplaceSettings::getInstance()->setDetailsPageId($detailPage->getCollectionID());
            MarketplaceSettings::getInstance()->setOverviewPageId($overviewPage->getCollectionID());
            MarketplaceSettings::getInstance()->setDemoContentInstalled(true);
        }
    }

    private function uninstallDemoPages() {
        if (MarketplaceSettings::getInstance()->getDemoContentInstalled()) {
            Page::getById(MarketplaceSettings::getInstance()->getDetailsPageId())->delete();
            Page::getById(MarketplaceSettings::getInstance()->getOverviewPageId())->delete();
        }
    }

    private function installOrUpdateBlockTypeSet() {
        if(!is_object(Set::getByHandle('developer'))){
            Set::add('developer', t('Developer'));
        }
    }

    /*
     * Thanks to John Liddiard (aka JohntheFish) - www.c5magic.co.uk for the following code concept.
     */
    private function uninstallBlockTypeSet() {
        $devSet = Set::getByHandle('developer');

        $btAddonList = BlockType::getByHandle('addon_list');

        if(is_object($btAddonList)){
            $btAddonListId = $btAddonList->getBlockTypeID();
        }

        $btAddonDetails = BlockType::getByHandle('addon_details');

        if(is_object($btAddonDetails)){
            $btAddonDetailsId = $btAddonDetails->getBlockTypeID();
        }

        if (is_object($devSet) && !empty($btAddonListId) && !empty($btAddonDetailsId)) {
            $numberOfBlockTypesInSet = intval(Database::connection()->fetchColumn(
                'SELECT COUNT(*) FROM BlockTypeSetBlockTypes WHERE btsID = ? AND NOT (btID = ? OR btID = ?)',

                array(
                    $devSet->getBlockTypeSetID(),
                    $btAddonDetailsId,
                    $btAddonListId
                )
            ));

            if ($numberOfBlockTypesInSet === 0) {
                $devSet->delete();
            }
        }
    }

    private function installOrUpdate() {
        $this->installOrUpdateJobs();
        $this->installOrUpdateBlockTypes();
        $this->installOrUpdatesPages();

        if (intval(Request::getInstance()->request->get("installDemoContent")) === 1) {
            $this->installOrUpdateDemoPages();
        }
    }

    private function preInstallOrUpdate() {
        $this->installOrUpdateBlockTypeSet();
    }

    public function upgrade() {
        $this->installOrUpdate();

        parent::upgrade();
    }

    public function install() {
        $this->initComponents();

        $this->preInstallOrUpdate();

        // install basic package
        parent::install();

        MarketplaceSettings::getInstance()->init();

        // install block elements, demo content etc.
        $this->installOrUpdate();

        // sync add-ons
        MarketplaceSettings::getInstance()->setUsername(Request::getInstance()->request->get("username"));
        MarketplaceSettings::getInstance()->setPassword(Request::getInstance()->request->get("password"));

        // import the first 10 items
        //MarketplaceAddons::getInstance()->syncItemsSynchronously(10);
    }

    private function uninstallConfigValues() {
        Database::connection()->executeQuery("DELETE FROM Config WHERE configNamespace = ?", array($this->pkgHandle));
    }

    public function uninstall() {
        // uninstall demo content
        $this->uninstallDemoPages();

        // uninstall block type set if required
        $this->uninstallBlockTypeSet();

        // cleanup settings
        $this->uninstallConfigValues();

        // cleanup database
        Database::connection()->executeQuery("DROP TABLE MarketplaceAddonImage");
        Database::connection()->executeQuery("DROP TABLE MarketplaceAddon");

        // uninstall package
        parent::uninstall();
    }

}
