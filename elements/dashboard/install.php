<?php

/**
 * @project:   Addon list add-on for concrete5
 * 
 * @author     Fabian Bitter
 * @copyright  (C) 2017 Fabian Bitter (www.bitter.de)
 * @version    1.0
 */

defined('C5_EXECUTE') or die('Access denied');

Core::make('help')->display(t("If problems occur, please increase the max_execution_time setting in your PHP settings."));

?>

<fieldset>
    <legend>
            <?php echo t("concrete5 login credentials"); ?>
    </legend>
        
    <p>
        <?php echo t("Please enter the login credentials of your concrete5 account."); ?>
    </p>

    <div class="form-group">
        <?php echo Core::make("helper/form")->label("username", t("Username")); ?>
        <?php echo Core::make("helper/form")->text("username"); ?>
    </div>

    <div class="form-group">
        <?php echo Core::make("helper/form")->label("password", t("Password")); ?>
        <?php echo Core::make("helper/form")->password("password"); ?>
    </div>
    
    <div class="help-block">
        <?php echo t("Data protection notice: Your login data is stored exclusively and encrypted in your database and transferred to concrete5.org only for authentication using an encrypted HTTPS connection. At the development of this add-on great importance was attached to the highest level of security so that even on the settings page on the Dashboard your password will only be transferred encoded."); ?>
    </div>
</fieldset>

<fieldset>
    <legend>
            <?php echo t("Demo Content"); ?>
    </legend>
    
    <div class="checkbox">
        <label>
            <input type="checkbox" name="installDemoContent" value="1" checked>
            
            <?php echo t("Install Demo Content"); ?>
        </label>
    </div>
</fieldset>