<?php

/**
 * @project:   Addon List
 * 
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2017 Fabian Bitter
 * @version    0.0.4.7
 */
defined('C5_EXECUTE') or die('Access denied');

Core::make('help')->display(t("If you need support please click <a href=\"%s\">here</a>.", "https://bitbucket.org/fabianbitter/addon_list/issues/new"));