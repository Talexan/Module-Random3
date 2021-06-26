<?php
    namespace Talexan\Random3\Block;

  
    class RandomThreeProducts extends \Magento\Catalog\Block\Product\ListProduct
    {
    /**
     * inheritdoc
     *
     * @return string
     */
    protected function _toHtml()
    {
        return parent::_toHtml();
    }
    /**
     * Get Cache Lifetime
     *
     * @return int
     */
        public function getCacheLifetime(){
            return null;
        }
    
    /**
     * GetCacheKey
     *
     * @return string
     */
    public function getCacheKey()
    {
        return 'talexan_custom_sidebar';
    }

    }