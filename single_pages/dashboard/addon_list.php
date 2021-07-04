<?php

/**
 * @project:   Addon list add-on for concrete5
 *
 * @author     Fabian Bitter
 * @copyright  (C) 2017 Fabian Bitter (www.bitter.de)
 * @version    1.0
 */

defined('C5_EXECUTE') or die('Access denied');

Core::make('help')->display(t("You can use the <a href=\"%s\">Automated Jobs</a> page to update your add-ons at any time. It is recommended to create a cronjob for this task so that your data is always up-to-date.", $this->url("/dashboard/system/optimization/jobs")));

View::element('/dashboard/Reminder', array("packageHandle" => "addon_list", "rateUrl" => "https://www.concrete5.org/marketplace/addons/addon-list/reviews"), 'addon_list');

?>

<?php \Concrete\Core\View\View::element('/dashboard/license_check', array("packageHandle" => "addon_list"), 'addon_list'); ?>

<form action="#" method="post">
    <fieldset>
        <legend>
            <?php echo t("concrete5 login credentials"); ?>
        </legend>

        <p>
            <?php echo t("Please enter the login credentials of your concrete5 account."); ?>
        </p>

        <div class="form-group">
            <?php echo $form->label("username", t("Username")); ?>
            <?php echo $form->text("username", $settings->getUsername()); ?>
        </div>

        <div class="form-group">
            <?php echo $form->label("password", t("Password")); ?>
            <?php echo $form->password("password", $settings->getMaskedPassword()); ?>
        </div>
    </fieldset>

    <?php \Concrete\Core\View\View::element('/dashboard/did_you_know', array("packageHandle" => "addon_list"), 'addon_list'); ?>

    <div class="ccm-dashboard-form-actions-wrapper">
        <div class="ccm-dashboard-form-actions">
            <div class="pull-right">
                <button type="submit" class="btn btn-primary">
                    <i class="fa fa-save" aria-hidden="true"></i> <?php echo t("Save"); ?>
                </button>
            </div>
        </div>
    </div>
</form>
