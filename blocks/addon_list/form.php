<?php

/**
 * @project:   Addon list add-on for concrete5
 *
 * @author     Fabian Bitter
 * @copyright  (C) 2017 Fabian Bitter (www.bitter.de)
 * @version    1.0
 */

defined("C5_EXECUTE") or die("Access Denied.");

Core::make('help')->display(t("Leave the Internal product page field blank if you want the user to be redirect directly to concrete5.org as soon as he clicks on one of the add-ons."));

$defaults = array();

$defaults['value'] = $value;
$defaults['className'] = 'ccm-widget-colorpicker';
$defaults['showInitial'] = true;
$defaults['showInput'] = true;
$defaults['primaryEmpty'] = true;
$defaults['cancelText'] = t('Cancel');
$defaults['chooseText'] = t('Choose');
$defaults['preferredFormat'] = 'hex';
$defaults['clearText'] = t('Clear Color Selection');

$formColor = Core::make('helper/form/color');

?>

<?php \Concrete\Core\View\View::element('/dashboard/license_check', array("packageHandle" => "addon_list"), 'addon_list'); ?>

<fieldset>
    <legend>
        <?php echo t("General"); ?>
    </legend>

    <div class="form-group">
        <?php echo $form->label("marketplaceType", t("Marketplace")); ?>
        <?php echo $form->select("marketplaceType", $marketplaceTypes, $marketplaceType); ?>
    </div>

    <div class="form-group">
        <?php echo $form->label("sortColumn", t("Sort by")); ?>
        <?php echo $form->select("sortColumn", array("title" => t("Title"), "downloadStats" => t("Number of downloads"), "singlePrice" => t("Price"), "starRatingAverage" => t("Rating")), $sortColumn); ?>
    </div>

    <div class="form-group">
        <?php echo $form->label("sortOrder", t("Sort order")); ?>
        <?php echo $form->select("sortOrder", array("asc" => t("Ascending"), "desc" => t("Descending")), $sortOrder); ?>
    </div>

    <div class="form-group">
        <?php echo $form->label("hasPagination", t("Pagination")); ?>
        <?php echo $form->select("hasPagination", array(0 => t("Disabled"), 1 => t("Enabled")), $hasPagination); ?>
    </div>

    <div class="form-group">
        <?php echo $form->label("itemsPerPage", t("Items per page")); ?>
        <?php echo $form->number("itemsPerPage", $itemsPerPage); ?>
    </div>

    <hr>

    <div class="form-group">
        <?php echo $form->label("backgroundColor", t("Background")); ?>
        <?php echo $formColor->output("backgroundColor", $backgroundColor, $defaults); ?>
    </div>

    <div class="form-group">
        <?php echo $form->label("borderColor", t("Border")); ?>
        <?php echo $formColor->output("borderColor", $borderColor, $defaults); ?>
    </div>

    <div class="form-group">
        <?php echo $form->label("textColor", t("Text")); ?>
        <?php echo $formColor->output("textColor", $textColor, $defaults); ?>
    </div>

    <div class="form-group">
        <?php echo $form->label("priceColor", t("Price")); ?>
        <?php echo $formColor->output("priceColor", $priceColor, $defaults); ?>
    </div>

    <div class="form-group">
        <?php echo $form->label("footerColor", t("Footer")); ?>
        <?php echo $formColor->output("footerColor", $footerColor, $defaults); ?>
    </div>

    <hr>

    <div class="form-group">
        <?php echo $form->label("productPageId", t("Internal product page")); ?>
        <?php echo Core::make('helper/form/page_selector')->selectPage("productPageId", $productPageId); ?>

        <p class="help-block">
            <?php echo t("Link to product detail page or leave blank for user to be directed to marketplace page."); ?>
        </p>
    </div>
</fieldset>


<fieldset>
    <legend>
        <?php echo t("Rating"); ?>
    </legend>

    <div class="form-group">
        <?php echo $form->label("normalStarColor", t("Star (normal)")); ?>
        <?php echo $formColor->output("normalStarColor", $normalStarColor, $defaults); ?>
    </div>

    <div class="form-group">
        <?php echo $form->label("activeStarColor", t("Star (active)")); ?>
        <?php echo $formColor->output("activeStarColor", $activeStarColor, $defaults); ?>
    </div>

    <div class="form-group">
        <?php echo $form->label("reviewTextColor", t("Text")); ?>
        <?php echo $formColor->output("reviewTextColor", $reviewTextColor, $defaults); ?>
    </div>
</fieldset>

<fieldset>
    <legend>
        <?php echo t("Pagination"); ?>
    </legend>

    <div class="form-group">
        <?php echo $form->label("paginationItemBackgroundColor", t("Background")); ?>
        <?php echo $formColor->output("paginationItemBackgroundColor", $paginationItemBackgroundColor, $defaults); ?>
    </div>

    <div class="form-group">
        <?php echo $form->label("paginationItemTextColor", t("Text")); ?>
        <?php echo $formColor->output("paginationItemTextColor", $paginationItemTextColor, $defaults); ?>
    </div>

    <div class="form-group">
        <?php echo $form->label("paginationItemBorderColor", t("Border")); ?>
        <?php echo $formColor->output("paginationItemBorderColor", $paginationItemBorderColor, $defaults); ?>
    </div>

    <div class="form-group">
        <?php echo $form->label("paginationItemActiveTextColor", t("Text (active)")); ?>
        <?php echo $formColor->output("paginationItemActiveTextColor", $paginationItemActiveTextColor, $defaults); ?>
    </div>

    <div class="form-group">
        <?php echo $form->label("paginationItemActiveBackgroundColor", t("Background (active)")); ?>
        <?php echo $formColor->output("paginationItemActiveBackgroundColor", $paginationItemActiveBackgroundColor, $defaults); ?>
    </div>
</fieldset>

<style type="text/css">
    .form-group {
        clear: both;
    }

    .ccm-widget-colorpicker {
        float: right;
    }

    .ui-dialog {
        overflow: visible !important;
    }
</style>

<?php \Concrete\Core\View\View::element('/dashboard/did_you_know', array("packageHandle" => "addon_list"), 'addon_list'); ?>
