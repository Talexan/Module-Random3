<?php
    namespace Talexan\Random3\Block\Random\ThreeProducts;

  
    class ListProduct extends \Magento\Catalog\Block\Product\ListProduct
    {
    
    /**
     *  Get Category Id
     * @return string
     */
    public function prepareDataCategoryId(){

        $registryObject = \Magento\Framework\App\ObjectManager::getInstance()->get(
                          \Magento\Framework\Registry::class);
        
        $currentCategory = $registryObject->registry('current_category');

        $sessionObject = \Magento\Framework\App\ObjectManager::getInstance()->get(
                         \Magento\Catalog\Model\Session::class);

        if(isset($currentCategory))
            $currentCategoryId = $currentCategory->getId();
        else
            $currentCategoryId = ($sessionObject->getLastVisitedCategotyId())?:$sessionObject->getLastViewedCategotyId();

        $result = ($currentCategoryId)?: 2; // this should not be, but if suddenly, then - root category 2

        return strval($result);
    }

    }