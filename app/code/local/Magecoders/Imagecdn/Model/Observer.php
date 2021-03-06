<?php
/**
 * Magecoders_Imagecdn
 *
 * NOTICE OF LICENSE 
 *
 * This source file is subject to the Open Software License (OSL 3.0), a
 * copy of which is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @category   Magecoders
 * @package    Magecoders_Imagecdn
 * @author     Magecoders Codemaster <codemaster@magecoders.com>
 * @copyright  Copyright (c) 2009 One Pica, Inc.
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

/**
 * Observers.
 */
class Magecoders_Imagecdn_Model_Observer extends Mage_Core_Model_Abstract
{
	/**
	 * Observer to clean the cache every so often since there could be URLs that aren't
	 * being used anymore. Without this, those URLs would never leave the cache.
	 *
	 * @param Mage_Cron_Model_Schedule $schedule
	 * @return (none)
	 */
    public function cleanCache($schedule) {
    	Mage::getSingleton('imagecdn/cache')->cleanCache();
    }
    
	/**
	 * When an admin config setting related to the this extension is changed, the cache
	 * must be cleared because the cache usually isn't relevant anymore. For example, if
	 * a user change the CDN type from Amazon S3 to FTP, the cache for Amazon shouldn't
	 * be used for the FTP CDN. The method also runs the adapter specific method as well.
	 *
	 * @param Mage_Cron_Model_Schedule $schedule
	 * @return bool
	 */
    public function onConfigChange($schedule) {
    	Mage::getSingleton('imagecdn/cache')->clearCache();
    	return Mage::Helper('imagecdn')->factory()->onConfigChange();
    }
    
	public function onSaveProduct($observer){
		$product =  $observer->getEvent()->getProduct();
		$galleryImages = $product->getMediaGalleryImages();
		
		$mediaPath = Mage::getBaseDir('media') . DS . 'catalog' . DS . 'product';
		
		if($galleryImages->count()){
			
			$aws = Mage::helper('imagecdn')->factory();
		
		
			 $images = new Varien_Data_Collection();
		
			foreach($galleryImages as $image){

					$url = $image->getUrl();
					$awsFilePath = '/catalog/product'.$image->getFile();
					$mageFilePath = $image->getPath();
										
					if($image->getRemoved()){
						$aws->remove($awsFilePath);
					}else{
						$result = $aws->save($awsFilePath,$mageFilePath);

						if($result){
							//@unlink($mageFilePath);
						}
					}
			}
			
		
			
		}
	}
	
}
