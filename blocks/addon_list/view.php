<?php

/**
 * @project:   Addon list add-on for concrete5
 * 
 * @author     Fabian Bitter
 * @copyright  (C) 2017 Fabian Bitter (www.bitter.de)
 * @version    1.0
 */

defined("C5_EXECUTE") or die("Access Denied.");

$navHelper = Core::make('helper/navigation');
?>

<?php if (is_array($items) && count($items) > 0): ?>
    <div class="addon-items">
        <?php foreach($items as $item): ?>
            <div class="addon-item <?php echo $item->getSkillLevelCssClass(); ?>">
                <div class="addon-item-inner" style="border-color: <?php echo $borderColor; ?>; background-color: <?php echo $backgroundColor; ?>;">
                    <div class="addon-item-header">
                        <?php
                            $productPage = Page::getById($productPageId);
                            
                            $isInternal = $productPageId > 0 && is_object($productPage);
                            
                            if ($isInternal) {
                                $productPageUrl = $navHelper->getCollectionURL($productPage->getCollectionPath()) . "?addon_item=" . $item->getSlug();
                            } else {
                                $productPageUrl = $item->getMarketplacePageUrl();
                            }
                        ?>
                        
                        <a href="<?php echo $productPageUrl; ?>" <?php echo $isInternal ? "" : " target=\"_blank\""; ?>>
                            <img src="<?php echo $item->getIconUrl(); ?>" alt="<?php echo $item->getTitle(); ?>" width="50" height="50" class="icon">

                            <span class="title" style="color: <?php echo $textColor; ?>">
                                <?php echo $item->getTitle(); ?>
                            </span>

                            <span class="buy-button" style="background-color: <?php echo $borderColor; ?>; color: <?php echo $priceColor; ?>;">
                                <?php if (intval($item->getSinglePrice()) === 0): ?>
                                    <?php echo t("Free"); ?>
                                <?php else: ?>
                                    <?php echo t("$%s", $item->getSinglePrice()); ?>
                                <?php endif; ?>
                            </span>
                        </a>
                    </div>

                    <div class="addon-item-body">
                        <div class="addon-rating">
                            <div class="addon-star-rating">
                                <?php for($i = 1; $i <= 5; $i++): ?>
                                    <?php if ($i <= round($item->getStarRatingAverage())): ?>
                                        <div class="star active" style="color: <?php echo $activeStarColor; ?>">
                                            <i class="fa fa-star" aria-hidden="true"></i>
                                        </div>
                                    <?php else: ?>
                                        <div class="star" style="color: <?php echo $normalStarColor; ?>">
                                            <i class="fa fa-star-o" aria-hidden="true"></i>
                                        </div>
                                    <?php endif; ?>
                                <?php endfor; ?>
                            </div>

                            <a href="<?php echo $item->getMarketplaceReviewUrl(); ?>" class="addon-reviews" style="color: <?php echo $reviewTextColor; ?>" target="_blank">
                                <?php if (intval($item->getStarRatingCount()) === 1): ?>
                                    <?php echo t("1 Review"); ?>
                                <?php else: ?>
                                    <?php echo t("%s Reviews", $item->getStarRatingCount()); ?>
                                <?php endif; ?>
                            </a>
                        </div>
                        
                        <p class="description" style="color: <?php echo $textColor; ?>">
                            <?php echo $item->getShortDescription(); ?>
                        </p>
                    </div>
                    
                    <div class="addon-item-footer" style="color: <?php echo $footerColor; ?>; background-color: <?php echo $borderColor; ?>;">
                        <p class="download-counter">
                            <?php echo t("%s Downloads", $item->getDownloadStats()); ?>
                        </p>
                        
                        <img src="<?php echo $item->getSkillLevelImageUrl(); ?>" alt="<?php echo $item->getSkillLevelName(); ?>" class="skill-level" width="30" height="30" />
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <?php if (intval($hasPagination) === 1): ?>
        <div class="addon-pagination">
            <?php
                $paginator = Core::make("Paginator");
                
                $paginator->setTotalItems($countOfItems);
                $paginator->setItemsPerPage($itemsPerPage);
                $paginator->setCurrentPage($currentPage);
                $paginator->setUrlPattern($navHelper->getCollectionURL(Page::getCurrentPage()->getCollectionPath()) . "?addon_page=(:num)");
                $paginator->setPreviousText(t("Back"));
                $paginator->setNextText(t("Next"));
                
                print $paginator->toHtml();
            ?>
        </div>

        <style type="text/css">
            .addon-pagination ul.pagination {
                 border-color: <?php echo $paginationItemBorderColor; ?> !important;
            }
                    
            .addon-pagination ul.pagination li a {
                 color: <?php echo $paginationItemTextColor; ?> !important;
                 background-color: <?php echo $paginationItemBackgroundColor; ?> !important;
            }
                    
            .addon-pagination ul.pagination li.active a,
            .addon-pagination ul.pagination li a:hover {
                 color: <?php echo $paginationItemActiveTextColor; ?> !important;
                 background-color: <?php echo $paginationItemActiveBackgroundColor; ?> !important;
            }
                    
        </style>
    <?php endif; ?>

<?php else: ?>
    <p class="addon-error">
        <?php echo t("No add-ons available. Please go to the <a href=\"%s\">Automated Jobs</a> page and run the \"Synchronize add-ons\" job to import your marketplace add-ons.", $this->url("/dashboard/system/optimization/jobs")); ?>
    </p>
<?php endif; ?>
