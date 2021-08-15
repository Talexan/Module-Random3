<?php
    namespace Talexan\Random3\Block;

  
    class RandomThreeProducts extends \Magento\Catalog\Block\Product\ListProduct
    {
    /**
     * @inheritdoc
     */
    protected function _toHtml()
    {
        $result = parent::_toHtml();

        return $result;
    }

    /**
     *  
     * @return 
     */
    public function saveCurrentCategoryInSession(){
        /**
         * First, we know the information about the current category. 
         * But, by the time we need it, it disappears. 
         * What is the question - just remember!
         * Secondly, getting it in a block may be logically wrong, 
         * for dynamically changing data - due to page caching.
         * Let's test it!
         * Otherwise, it seems logical to receive this information when accessing the server, 
         * that is, in the controller. But the controller is in 
         * the Catalog module. That is, you need a plugin.
         * Well, or, if I remember correctly, there are some events 
         * and an Observer is needed.
         * try {
         *      $this->_eventManager->dispatch(
         *      'catalog_controller_category_init_after',
         *      ['category' => $category, 'controller_action' => $this]);
         *     } catch (LocalizedException $e) {
         *             $this->logger->critical($e);
         *             return false;
         *     }
         */

        $registryObject = \Magento\Framework\App\ObjectManager::getInstance()->get(
                          \Magento\Framework\Registry::class);

        $sessionObject = \Magento\Framework\App\ObjectManager::getInstance()->get(
                         \Talexan\Random3\Model\Session::class);
        
        $currentCategory = $registryObject->registry('current_category');

        if(!isset($currentCategory)){
            return false;
        }

        $sessionObject->setForRandom3CategoryId($currentCategory->getId());

        return true;
    }

    /**
     * Write log system
     * @param string $prefix
     * @param string $info
     * @return void
     */
    private function writeLog(string $prefix, string $info){
        $logger =  \Magento\Framework\App\ObjectManager::getInstance()->get(\Magento\Framework\Logger\Monolog::class);
        $logger->info($prefix . $info);
    }
    }