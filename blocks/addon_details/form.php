<?php

/**
 * @project:   Addon list add-on for concrete5
 *
 * @author     Fabian Bitter
 * @copyright  (C) 2017 Fabian Bitter (www.bitter.de)
 * @version    1.0
 */

defined("C5_EXECUTE") or die("Access Denied.");

Core::make('help')->display(t("If you choose the automatic detection option, the add-on to be displayed is determined by the parameters of the HTTP request header."));

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
        <?php echo $form->label("slug", t("Add-on")); ?>
        <?php echo $form->select("slug", $items, $slug); ?>
    </div>
</fieldset>

<fieldset>
    <legend>
        <?php echo t("Icon"); ?>
    </legend>

    <div class="form-group">
        <?php echo $form->label("iconVisible", t("Visible")); ?>
        <?php echo $form->select("iconVisible", array(0 => t("No"), 1 => t("Yes")), intval($iconVisible)); ?>
    </div>
</fieldset>

<fieldset>
    <legend>
        <?php echo t("Title"); ?>
    </legend>

    <div class="form-group">
        <?php echo $form->label("titleVisible", t("Visible")); ?>
        <?php echo $form->select("titleVisible", array(0 => t("No"), 1 => t("Yes")), intval($titleVisible)); ?>
    </div>

    <div class="form-group">
        <?php echo $form->label("titleColor", t("Title")); ?>
        <?php echo $formColor->output("titleColor", $titleColor, $defaults); ?>
    </div>

    <div class="form-group">
        <?php echo $form->label("subtitleColor", t("Subtitle")); ?>
        <?php echo $formColor->output("subtitleColor", $subtitleColor, $defaults); ?>
    </div>
</fieldset>

<fieldset>
    <legend>
        <?php echo t("Rating"); ?>
    </legend>

    <div class="form-group">
        <?php echo $form->label("ratingVisible", t("Visible")); ?>
        <?php echo $form->select("ratingVisible", array(0 => t("No"), 1 => t("Yes")), intval($ratingVisible)); ?>
    </div>

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
        <?php echo t("Skill level"); ?>
    </legend>

    <div class="form-group">
        <?php echo $form->label("skillLevelVisible", t("Visible")); ?>
        <?php echo $form->select("skillLevelVisible", array(0 => t("No"), 1 => t("Yes")), intval($skillLevelVisible)); ?>
    </div>
</fieldset>

<fieldset>
    <legend>
        <?php echo t("Buybutton (single)"); ?>
    </legend>

    <div class="form-group">
        <?php echo $form->label("singleButtonVisible", t("Visible")); ?>
        <?php echo $form->select("singleButtonVisible", array(0 => t("No"), 1 => t("Yes")), intval($singleButtonVisible)); ?>
    </div>

    <div class="form-group">
        <?php echo $form->label("singleButtonTextColor", t("Text")); ?>
        <?php echo $formColor->output("singleButtonTextColor", $singleButtonTextColor, $defaults); ?>
    </div>

    <div class="form-group">
        <?php echo $form->label("singleButtonBackgroundColor", t("Background")); ?>
        <?php echo $formColor->output("singleButtonBackgroundColor", $singleButtonBackgroundColor, $defaults); ?>
    </div>
</fieldset>

<fieldset>
    <legend>
        <?php echo t("Buybutton (package)"); ?>
    </legend>

    <div class="form-group">
        <?php echo $form->label("packageButtonVisible", t("Visible")); ?>
        <?php echo $form->select("packageButtonVisible", array(0 => t("No"), 1 => t("Yes")), intval($packageButtonVisible)); ?>
    </div>

    <div class="form-group">
        <?php echo $form->label("packageButtonTextColor", t("Text")); ?>
        <?php echo $formColor->output("packageButtonTextColor", $packageButtonTextColor, $defaults); ?>
    </div>

    <div class="form-group">
        <?php echo $form->label("packageButtonBackgroundColor", t("Background")); ?>
        <?php echo $formColor->output("packageButtonBackgroundColor", $packageButtonBackgroundColor, $defaults); ?>
    </div>

    <div class="form-group">
        <?php echo $form->label("packageButtonDiscountColor", t("Discount")); ?>
        <?php echo $formColor->output("packageButtonDiscountColor", $packageButtonDiscountColor, $defaults); ?>
    </div>
</fieldset>

<fieldset>
    <legend>
        <?php echo t("Quote"); ?>
    </legend>

    <div class="form-group">
        <?php echo $form->label("quoteVisible", t("Visible")); ?>
        <?php echo $form->select("quoteVisible", array(0 => t("No"), 1 => t("Yes")), intval($quoteVisible)); ?>
    </div>

    <div class="form-group">
        <?php echo $form->label("quoteTextColor", t("Text")); ?>
        <?php echo $formColor->output("quoteTextColor", $quoteTextColor, $defaults); ?>
    </div>

    <div class="form-group">
        <?php echo $form->label("quoteAuthorLinkColor", t("Author")); ?>
        <?php echo $formColor->output("quoteAuthorLinkColor", $quoteAuthorLinkColor, $defaults); ?>
    </div>
</fieldset>

<fieldset>
    <legend>
        <?php echo t("Description"); ?>
    </legend>

    <div class="form-group">
        <?php echo $form->label("descriptionVisible", t("Visible")); ?>
        <?php echo $form->select("descriptionVisible", array(0 => t("No"), 1 => t("Yes")), intval($descriptionVisible)); ?>
    </div>

    <div class="form-group">
        <?php echo $form->label("descriptionColor", t("Text")); ?>
        <?php echo $formColor->output("descriptionColor", $descriptionColor, $defaults); ?>
    </div>
</fieldset>

<fieldset>
    <legend>
        <?php echo t("Specifications"); ?>
    </legend>

    <div class="form-group">
        <?php echo $form->label("specificationsVisible", t("Visible")); ?>
        <?php echo $form->select("specificationsVisible", array(0 => t("No"), 1 => t("Yes")), intval($specificationsVisible)); ?>
    </div>

    <div class="form-group">
        <?php echo $form->label("specificationsBackgroundColor", t("Background")); ?>
        <?php echo $formColor->output("specificationsBackgroundColor", $specificationsBackgroundColor, $defaults); ?>
    </div>

    <div class="form-group">
        <?php echo $form->label("specificationsBorderColor", t("Border")); ?>
        <?php echo $formColor->output("specificationsBorderColor", $specificationsBorderColor, $defaults); ?>
    </div>

    <div class="form-group">
        <?php echo $form->label("specificationsTextColor", t("Text")); ?>
        <?php echo $formColor->output("specificationsTextColor", $specificationsTextColor, $defaults); ?>
    </div>

    <div class="form-group">
        <?php echo $form->label("specificationsValueColor", t("Value")); ?>
        <?php echo $formColor->output("specificationsValueColor", $specificationsValueColor, $defaults); ?>
    </div>

    <div class="form-group">
        <?php echo $form->label("specificationsCheckedColor", t("Tests")); ?>
        <?php echo $formColor->output("specificationsCheckedColor", $specificationsCheckedColor, $defaults); ?>
    </div>
</fieldset>

<fieldset>
    <legend>
        <?php echo t("Gallery"); ?>
    </legend>

    <div class="form-group">
        <?php echo $form->label("galleryVisible", t("Visible")); ?>
        <?php echo $form->select("galleryVisible", array(0 => t("No"), 1 => t("Yes")), intval($galleryVisible)); ?>
    </div>

    <div class="form-group">
        <?php echo $form->label("galleryBackgroundColor", t("Background")); ?>
        <?php echo $formColor->output("galleryBackgroundColor", $galleryBackgroundColor, $defaults); ?>
    </div>

    <div class="form-group">
        <?php echo $form->label("galleryBorderColor", t("Border")); ?>
        <?php echo $formColor->output("galleryBorderColor", $galleryBorderColor, $defaults); ?>
    </div>

    <div class="form-group">
        <?php echo $form->label("galleryFooterBackgroundColor", t("Footer background")); ?>
        <?php echo $formColor->output("galleryFooterBackgroundColor", $galleryFooterBackgroundColor, $defaults); ?>
    </div>

    <div class="form-group">
        <?php echo $form->label("galleryFooterLinkNormalColor", t("Footer link (normal)")); ?>
        <?php echo $formColor->output("galleryFooterLinkNormalColor", $galleryFooterLinkNormalColor, $defaults); ?>
    </div>

    <div class="form-group">
        <?php echo $form->label("galleryFooterLinkHoverColor", t("Footer link (hover)")); ?>
        <?php echo $formColor->output("galleryFooterLinkHoverColor", $galleryFooterLinkHoverColor, $defaults); ?>
    </div>

    <div class="form-group">
        <?php echo $form->label("galleryFooterLinkDisabledColor", t("Footer link (disabled)")); ?>
        <?php echo $formColor->output("galleryFooterLinkDisabledColor", $galleryFooterLinkDisabledColor, $defaults); ?>
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
