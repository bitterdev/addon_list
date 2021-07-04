<?php

/**
 * @project:   Addon list add-on for concrete5
 * 
 * @author     Fabian Bitter
 * @copyright  (C) 2017 Fabian Bitter (www.bitter.de)
 * @version    1.0
 */

defined("C5_EXECUTE") or die("Access Denied.");

?>

<div class="addon-container <?php echo !$galleryVisible && !$specificationsVisible ? "full-width" : ""; ?>">
    <div class="addon-left-column">
        <?php if ($iconVisible): ?>
            <img src="<?php echo $item->getIconUrl(); ?>" alt="<?php echo $item->getTitle(); ?>" class="addon-icon" width="97" height="97">
        <?php endif; ?>

        <div class="addon-info">
            <?php if ($titleVisible): ?>
                <h2 class="addon-title" style="color: <?php echo $titleColor ?>;">
                    <?php echo $item->getTitle(); ?>
                </h2>
            
                <span class="author-info" style="color: <?php echo $subtitleColor; ?>">
                    <?php echo t("Developed by <a href=\"%s\" style=\"color: %s;\" target=\"_blank\">%s</a>.", $item->getProfileUrl(), $subtitleColor, $item->getProfileName()); ?>
                </span>
            <?php endif; ?>
            
            <?php if ($ratingVisible): ?>
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
            <?php endif; ?>
            
            <?php if ($skillLevelVisible): ?>
                <div class="addon-skill-level <?php echo $item->getSkillLevelCssClass(); ?>">
                    <img src="<?php echo $item->getSkillLevelImageUrl(); ?>" alt="<?php echo $item->getSkillLevelName(); ?>" class="addon-skill-level-icon" width="30" height="30">

                    <span class="addon-skill-level-name">
                        <?php echo $item->getSkillLevelName(); ?>
                    </span>
                </div>
            <?php endif; ?>
            
            <div class="addon-buttons">
                <?php if ($singleButtonVisible): ?>
                    <a href="<?php echo $item->getBuySingleItemLinkUrl(); ?>" class="addon-buy-button" style="color: <?php echo $singleButtonTextColor; ?>; background-color: <?php echo $singleButtonBackgroundColor; ?>" target="_blank">
                        <span class="price">
                            <?php if (intval($item->getSinglePrice()) === 0): ?>
                                <?php echo t("Free"); ?>
                            <?php else: ?>
                                <?php echo t("$%s", $item->getSinglePrice()); ?>
                            <?php endif; ?>
                        </span>

                        <span class="quantity">
                            <?php echo t("Purchase 1"); ?>
                        </span>
                    </a>
                <?php endif; ?>

                <?php if ($packageButtonVisible && $item->hasPackageSKU()): ?>
                    <a href="<?php echo $item->getBuyPackageItemLinkUrl(); ?>" class="addon-buy-button" style="color: <?php echo $packageButtonTextColor; ?>; background-color: <?php echo $packageButtonBackgroundColor; ?>" target="_blank">
                        <span class="price">
                            <?php if (intval($item->getPackagePrice()) === 0): ?>
                                <?php echo t("Free"); ?>
                            <?php else: ?>
                                <?php echo t("$%s", $item->getPackagePrice()); ?>
                            <?php endif; ?>
                        </span>

                        <span class="quantity">
                            <?php echo t("Purchase 5"); ?>
                        </span>
                    </a>
                
                    <?php if ($item->hasPackageDiscount()): ?>
                        <span class="discount" style="color: <?php echo $packageButtonDiscountColor; ?>;">
                            <?php echo t("Save %s%%", $item->getPackageDiscount()); ?>
                        </span>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
            
        <?php if ($item->getQuoteReview() && $quoteVisible): ?>
            <div class="addon-quote" style="color: <?php echo $quoteTextColor; ?>;">
                <?php echo $item->getQuoteReview(); ?>
                
                <a href="<?php echo $item->getQuoteLink(); ?>" class="author" style="color: <?php echo $quoteAuthorLinkColor; ?>;" target="_blank">
                    <?php echo $item->getQuoteAuthor(); ?>
                </a>
            </div>
        <?php endif; ?>
            
        <?php if ($descriptionVisible): ?>
            <div class="addon-description">
                <?php echo $item->getDescription(); ?>
            </div>
        <?php endif; ?>
    </div>

    <div class="addon-right-column">
        <?php if ($galleryVisible): ?>
            <div class="addon-gallery" style="background-color: <?php echo $galleryBackgroundColor; ?>; border-color: <?php echo $galleryBorderColor; ?>;">
                <div class="addon-gallery-body">
                    <a href="javascript:void(0);" class="addon-gallery-image">
                        <img src="<?php echo $item->getFirstScreenshotUrl(); ?>">
                    </a>
                </div>
                
                <div class="addon-gallery-footer" style="background-color: <?php echo $galleryFooterBackgroundColor; ?>; border-color: <?php echo $galleryBorderColor; ?>;">
                    <ul>
                        <li class="screenshots">
                            <a href="javascript:void(0);">
                                <?php echo t("Screenshots"); ?>
                            </a>
                        </li>
                        
                        <li class="video <?php echo ($item->hasVideoEmbedCode() ? "" : "disabled"); ?>">
                            <a href="javascript:void(0);" class="fancybox-video">
                                <?php echo t("Video"); ?>
                            </a>
                        </li>
                        
                        <li class="live-demo <?php echo ($item->hasLiveDemoUrl() ? "" : "disabled"); ?>">
                            <a href="<?php echo $item->hasLiveDemoUrl() ? $item->getLiveDemoUrl() : "javascript:void(0);"; ?>" target="_blank">
                                <?php echo t("Live Demo"); ?>
                            </a>
                        </li>
                        
                        <li class="showcase">
                            <a href="<?php echo $item->getShowcaseUrl(); ?>" target="_blank">
                                <?php echo t("Showcase"); ?>
                            </a>
                        </li>
                    </ul>
                </div>
                
                <style type="text/css">
                    .addon-gallery .addon-gallery-footer ul li a {
                         color: <?php echo $galleryFooterLinkNormalColor; ?> !important;
                    }
                    
                    .addon-gallery .addon-gallery-footer ul li.disabled a {
                        color: <?php echo $galleryFooterLinkDisabledColor; ?> !important;
                    }
                    
                    .addon-gallery .addon-gallery-footer ul li a:hover {
                        color: <?php echo $galleryFooterLinkHoverColor; ?> !important;
                    }
                    
                    .addon-gallery .addon-gallery-footer ul li.disabled a:hover {
                        color: <?php echo $galleryFooterLinkDisabledColor; ?> !important;
                    }
                </style>

                <div id="addon-screenshots" style="display: none;">
                    <?php foreach($item->getScreenshotList() as $screenshotUrl): ?>
                        <a class="fancybox-screenshot" rel="screenshot" href="<?php echo $screenshotUrl; ?>">
                            <img src="<?php echo $screenshotUrl; ?>" alt="" width="100%" height="100%">
                        </a>
                    <?php endforeach; ?>
                </div>

                <script>
                    var videoEmbedCode = "<?php echo addslashes($item->getVideoEmbedCode()); ?>";

                    $(document).ready(function() {
                        $(".fancybox-screenshot").fancyboxPlus({
                            transitionIn: 'none',
                            transitionOut: 'none',
                            changeFade: '',
                            easingIn: '',
                            easingOut: '',
                            speedIn: 0,
                            speedOut: 0,
                            changeSpeed: 0,
                            padding: 0
                        });

                        $(".addon-gallery .screenshots a, a.addon-gallery-image").unbind().bind("click", function() {
                            $("#addon-screenshots a:first-child").click();
                        });

                        $(".addon-gallery .fancybox-video").unbind().bind("click", function() {
                            if ($(this).parent().hasClass("disabled")) return;
                            
                            $.fancyboxPlus(
                                videoEmbedCode,

                                {
                                    'autoDimensions' : false,
                                    'width' : "600",
                                    'height' : 'auto',
                                    'transitionIn' : 'none',
                                    'transitionOut' : 'none'
                                }
                            );
                        });
                    });
                </script>
            </div>
        <?php endif; ?>
        
        <?php if ($specificationsVisible): ?>
            <div class="addon-specifications" style="color: <?php echo $specificationsTextColor; ?>; background-color: <?php echo $specificationsBackgroundColor; ?>; border-color: <?php echo $specificationsBorderColor; ?>;">
                <?php if ($item->getIsTheme()): ?>
                     <div class="col-left">
                        <div>
                            <span class="title">
                                <?php echo t("Customizable Style:"); ?>
                            </span>

                            <span style="color: <?php echo $specificationsValueColor; ?>;">
                                <?php echo $item->getHasCustomizableStyles() ? t("Yes") : t("No"); ?>
                            </span>
                        </div>

                        <div>
                            <span class="title">
                                <?php echo t("License:"); ?>
                            </span>

                            <span style="color: <?php echo $specificationsValueColor; ?>;">
                                <a href="<?php echo $item->getLicenseUrl(); ?>" target="_blank" style="color: <?php echo $valueColor; ?>;">
                                    <?php echo $item->getLicenseName(); ?>
                                </a>
                            </span>
                        </div>

                        <div>
                            <span class="title">
                                <?php echo t("Page types:"); ?>
                            </span>

                            <span style="color: <?php echo $specificationsValueColor; ?>;">
                                <?php echo str_replace(", ", "<br>", $item->getPageTypes()); ?>
                            </span>
                        </div>
                         
                        <div>
                            <span class="title">
                                <?php echo t("Current Version:"); ?>
                            </span>

                            <span style="color: <?php echo $specificationsValueColor; ?>;">
                                <?php echo $item->getVersionNumber(); ?>
                            </span>
                        </div>
                    </div>

                    <div class="col-right">
                        <div>
                            <span class="title">
                                <?php echo t("Compatible:"); ?>
                            </span>

                            <span style="color: <?php echo $specificationsValueColor; ?>;">
                                <?php echo $item->getCompatibleConcreteVersion(); ?>
                            </span>
                        </div>
                        
                        <div>
                            <span class="title">
                                <?php echo t("Support Response:"); ?>
                            </span>

                            <span style="color: <?php echo $specificationsValueColor; ?>;">
                                <?php echo $item->getSupportResponse(); ?>
                            </span>
                        </div>

                        <div>
                            <span class="title">
                                <?php echo t("Support Hosted:"); ?>
                            </span>

                            <span style="color: <?php echo $specificationsValueColor; ?>;">
                                <?php echo $item->getSupportHosted(); ?>
                            </span>
                        </div>

                        <div>
                            <span class="title">
                                <?php echo t("Marketplace Tests:"); ?>
                            </span>

                            <span style="color: <?php echo $specificationsValueColor; ?>;">
                                <?php if ($item->getPassedAutomatedTests()): ?>
                                    <div class="text-success" style="color: <?php echo $specificationsCheckedColor; ?>;">
                                        <i class="fa fa-check"></i>
                                        <?php echo t("Passed Automated Tests"); ?>
                                    </div>
                                <?php endif; ?>

                                <?php if ($item->getPassedPrbReview()): ?>
                                    <div class="text-success" style="color: <?php echo $specificationsCheckedColor; ?>;">
                                        <i class="fa fa-check"></i>
                                        <?php echo t("Passed PRB Review"); ?>
                                    </div>
                                <?php endif; ?>
                            </span>
                        </div>
                    </div>
                <?php else: ?>
                
                    <div class="col-left">
                        <div>
                            <span class="title">
                                <?php echo t("Current Version:"); ?>
                            </span>

                            <span style="color: <?php echo $specificationsValueColor; ?>;">
                                <?php echo $item->getVersionNumber(); ?>
                            </span>
                        </div>
                        
                        <div>
                            <span class="title">
                                <?php echo t("Fully Translatable:"); ?>
                            </span>

                            <span style="color: <?php echo $specificationsValueColor; ?>;">
                                <?php echo $item->getFullyTranslatable() ? t("Yes") : t("No"); ?>
                            </span>
                        </div>

                        <div>
                            <span class="title">
                                <?php echo t("Needs External Libraries:"); ?>
                            </span>

                            <span style="color: <?php echo $specificationsValueColor; ?>;">
                                <?php echo $item->getNeedsExternalLibraries() ? t("Yes") : t("No"); ?>
                            </span>
                        </div>

                        <div>
                            <span class="title">
                                <?php echo t("Compatible:"); ?>
                            </span>

                            <span style="color: <?php echo $specificationsValueColor; ?>;">
                                <?php echo $item->getCompatibleConcreteVersion(); ?>
                            </span>
                        </div>

                        <div>
                            <span class="title">
                                <?php echo t("License:"); ?>
                            </span>

                            <span style="color: <?php echo $specificationsValueColor; ?>;">
                                <a href="<?php echo $item->getLicenseUrl(); ?>" target="_blank" style="color: <?php echo $valueColor; ?>;">
                                    <?php echo $item->getLicenseName(); ?>
                                </a>
                            </span>
                        </div>
                    </div>

                    <div class="col-right">
                        <div>
                            <span class="title">
                                <?php echo t("Support Response:"); ?>
                            </span>

                            <span style="color: <?php echo $specificationsValueColor; ?>;">
                                <?php echo $item->getSupportResponse(); ?>
                            </span>
                        </div>

                        <div>
                            <span class="title">
                                <?php echo t("Support Hosted:"); ?>
                            </span>

                            <span style="color: <?php echo $specificationsValueColor; ?>;">
                                <?php echo $item->getSupportHosted(); ?>
                            </span>
                        </div>

                        <div>
                            <span class="title">
                                <?php echo t("Needs extra server permissions:"); ?>
                            </span>

                            <span style="color: <?php echo $specificationsValueColor; ?>;">
                                <?php echo $item->getNeedsExtraServerPermissions() ? t("Yes") : t("No"); ?>
                            </span>
                        </div>

                        <div>
                            <span class="title">
                                <?php echo t("Needs Internet:"); ?>
                            </span>

                            <span style="color: <?php echo $specificationsValueColor; ?>;">
                                <?php echo $item->getNeedsInternet() ? t("Yes") : t("No"); ?>
                            </span>
                        </div>

                        <div>
                            <span class="title">
                                <?php echo t("Marketplace Tests:"); ?>
                            </span>

                            <span style="color: <?php echo $specificationsValueColor; ?>;">
                                <?php if ($item->getPassedAutomatedTests()): ?>
                                    <div class="text-success" style="color: <?php echo $specificationsCheckedColor; ?>;">
                                        <i class="fa fa-check"></i>
                                        <?php echo t("Passed Automated Tests"); ?>
                                    </div>
                                <?php endif; ?>

                                <?php if ($item->getPassedPrbReview()): ?>
                                    <div class="text-success" style="color: <?php echo $specificationsCheckedColor; ?>;">
                                        <i class="fa fa-check"></i>
                                        <?php echo t("Passed PRB Review"); ?>
                                    </div>
                                <?php endif; ?>
                            </span>
                        </div>
                    </div>
                <?php endif; ?>
                
            </div>
        <?php endif; ?>
    </div>
</div>