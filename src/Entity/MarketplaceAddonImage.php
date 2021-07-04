<?php

/**
 * @project:   Addon list add-on for concrete5
 * 
 * @author     Fabian Bitter
 * @copyright  (C) 2017 Fabian Bitter (www.bitter.de)
 * @version    1.0
 */

namespace Concrete\Package\AddonList\Src\Entity;

defined('C5_EXECUTE') or die('Access denied');

/**
 * @Entity
 * @Table(name="MarketplaceAddonImage")
 * */
class MarketplaceAddonImage {

    /**
     * @Id
     * @Column(type="integer")
     * @GeneratedValue(strategy="AUTO")
     */
    protected $id;
    
    /**
     * @Column(type="string")
     */
    protected $slug = '';

    /**
     * @Column(type="string")
     */
    protected $imageUrl = '';

    /**
     * @Column(type="integer")
     */
    protected $lastSync = '';
    
    public function getSlug() {
        return $this->slug;
    }

    public function getImageUrl() {
        return $this->imageUrl;
    }

    public function setSlug($slug) {
        $this->slug = $slug;
    }

    public function setImageUrl($imageUrl) {
        $this->imageUrl = $imageUrl;
    }
    
    public function getLastSync() {
        return $this->lastSync;
    }

    public function setLastSync($lastSync) {
        $this->lastSync = $lastSync;
    }
}
