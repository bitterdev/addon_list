<?php

/**
 * @project:   Addon list add-on for concrete5
 * 
 * @author     Fabian Bitter
 * @copyright  (C) 2017 Fabian Bitter (www.bitter.de)
 * @version    1.0
 */

namespace Concrete\Package\AddonList\Controller\SinglePage\Dashboard;

use Concrete\Core\Page\Controller\DashboardPageController;
use Concrete\Package\AddonList\Src\MarketplaceSettings;
use Concrete\Package\AddonList\Src\MarketplaceAddons;
use Request;
use Core;

class AddonList extends DashboardPageController {
    
    private function validate() {
        $val = Core::make('helper/validation/form');

        $val->setData($this->post());

        $val->addRequired("username", t("You must specify a valid username."));
        $val->addRequired("password", t("You must specify a valid password."));

        if ($val->test()) {
            $password = $this->post("password");
            
            if ($this->post("password") === MarketplaceSettings::getInstance()->getMaskedPassword()) {
                // password is still unchanged - used saved one.
                $password = MarketplaceSettings::getInstance()->getPassword();
            }
            
            if (MarketplaceAddons::getInstance()->verifyCredentials($this->post("username"), $password)) {
                return true;
                
            } else {
                $this->error->add(t("Invalid credentials."));
                
                return false;
            }
            
        } else {
            $this->error = $val->getError();

            return false;
        }
    }
    
    public function view() {
        $settings = MarketplaceSettings::getInstance();
        
        if (Request::getInstance()->isPost()) {
            if ($this->validate()) {
                $settings->setUsername($this->post("username"));
                
                if ($this->post("password") !== $settings->getMaskedPassword()) {
                    // password has been changed
                    $settings->setPassword($this->post("password"));
                }
                
                // print out success notice
                $this->set('success', t("The credentials has been successfully updated."));
            }
        }
        
        $this->set("settings", $settings);
    }
    
}