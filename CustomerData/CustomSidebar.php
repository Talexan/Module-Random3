<?php

namespace Talexan\Random3\CustomerData;

use Magento\Customer\CustomerData\SectionSourceInterface;
use Magento\Catalog\Model\Session;
use Magento\Framework\Escaper;
use Magento\Catalog\Model\CategoryFactory;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as ProductCollectionFactory;
use Magento\Catalog\Helper\Output as OutputHelper;
use Magento\Framework\App\ObjectManager;
use Magento\Catalog\Block\Product\ImageFactory;
use Magento\Framework\Pricing\Render;
use Magento\Catalog\Pricing\Price\FinalPrice;
use Magento\Framework\App\RequestFactory;
use Talexan\Random3\Model\Session as MySession;


class CustomSidebar implements SectionSourceInterface
{
    /**
     * @var Session $_catalogSession
     */
     protected $_catalogSession;

     /**
     * @var MySession $_mySession
     */
    protected $_mySession;

     /**
      * @var CategoryFactory
      */
    protected $_categoryFactory;

    /**
     * @var ProductCollectionFactory
     */
    protected $_productCollectionFactory;

    /**
     * @var Magento\Framework\Escaper
     */
    protected $_escaper;

    /**
     * @var Store
     */
    protected $_store;

    /**
     * @var OutputHelper
     */
    protected $_outputHelper;

    /**
     * @var ImageFactory
     */
    protected $_blockImageFactory;

    /**
     * @var Render
     */
    protected $_priceRender;

    /**
     * Request Factory
     * @var RequestFactory
     */
    protected $_requestFactory;

    /**
     * For testing
     */
    static protected $_staticData = [];

    /**
     * For testing
     */
    protected $_categoryId = null;

    /**
     * Constructor
     * @param Escaper $escaper
     * @param Session $catalogSession
     * @param MySession $mySession
     * @param CategoryFactory $categoryFactory
     * @param Escaper $escaper
     * @param ImageFactory $blockImageFactory
     * @param Render $priceRender
     * @param OutputHelper $outputHelper|null
     * @param RequestFactory
     */
    public function __construct(
        Session $catalogSession,
        MySession $mySession,
        CategoryFactory $categoryFactory,
        Escaper $escaper,
        ImageFactory $blockImageFactory,
        Render $priceRender,
        ?OutputHelper $outputHelper = null,
        RequestFactory $requestFactory
    )
    {
        $this->_catalogSession = $catalogSession;
        $this->_mySession = $mySession;
        $this->_categoryFactory = $categoryFactory;
        $this->_escaper = $escaper;
        $this->_outputHelper = $outputHelper ?? ObjectManager::getInstance()->get(OutputHelper::class);
        $this->_blockImageFactory = $blockImageFactory;
        $this->_priceRender = $priceRender;
        $this->_requestFactory = $requestFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function getSectionData()
    {
        $items = $this->getItems();
        $count = count($items);
        return [
            'count' => $count,
            'items' => $items
        ];
    }

    /**
     * @return array
     */
    protected function getItems()
    {    
        $productCollection = $this->getCollectionRandomThreeProductsInCategory($this->getCurrentCategory());    
        
        $this->writeLog('SQL = ', $productCollection->getSelect()->__toString());

        $items = [];
        foreach ($productCollection as $product) {
            $blockImage = $this->getBlockImage($product);
            $items[] = [
                'href' => $this->_escaper->escapeUrl($product->getProductUrl()),
                'name' => $this->getProductName($product), //?
                'src' => $blockImage->getImageUrl(),//cache?
                'classImg' => 'product-image-photo-sidebar', //$blockImage->getClass(),
                'labelImg' => $blockImage->getLabel(), //?
                'price' => __('$') . sprintf("%01.2f", round($product->getFinalPrice(1), 2))//?
            ];
        }
 
        return $items;
    }

    /**
     * Get current category
     * @return \Magento\Catalog\Model\Category
     */
    public function getCurrentCategory(){
        
        $currentCategory = $this->_categoryFactory->create()
                                ->load($this->getCurrentCategoryId()); 

        return $currentCategory;
    }

    /**
     * Get current category id
     * @return int id
     */
    public function getCurrentCategoryId(){
        $currentCategoryId = $this->_mySession->getForRandom3CategoryId();
        
        if (!$currentCategoryId){
            $currentCategoryId = $this->getCategoryId();

            if (!$currentCategoryId){
                $currentCategoryId = self::getStaticData();

                if (!$currentCategoryId){
                    $currentCategoryId = $this->_catalogSession->getLastViewedCategoryId();

                    if (!$currentCategoryId){
                        $currentCategoryId = $this->_catalogSession->getLastVisitedCategoryId(); 
                    }
                }
            }
        } 

        return $currentCategoryId? $currentCategoryId: 2; //Else - Root category
    }

    /**
     * Get random 3 products in category
     * @param \Magento\Catalog\Model\Category $category
     * @return \Magento\Catalog\Model\ResourceModel\Product\Collection|null
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getCollectionRandomThreeProductsInCategory(\Magento\Catalog\Model\Category $category){
        if($category instanceof \Magento\Catalog\Model\Category){
            $productCollection = $category->getProductCollection(); 
            $productCollection->addAttributeToSelect('*');
            $productCollection->addAttributeToFilter('visibility', 
                                \Magento\Catalog\Model\Product\Visibility::VISIBILITY_BOTH);
            // AND                    
            $productCollection->addAttributeToFilter('status',
                                \Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED);
            // AND
            //$productCollection->addAttributeToFilter('type_id',
            //                    ['eq' => 'simple']); // This is a requirement of the technical assignment?
            // But the products on the showcase are of the configure type?

            $productCollection->getSelect()->distinct()->orderRand()->limit(3, 0);

            return $productCollection;
        }
        else{
            throw new \Magento\Framework\Exception\LocalizedException(
                __('Sorry, but this is not what you need.'));
        }

        return null;

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

    /**
     * Get product image url
     * @param Magento\Catalog\Model\Product
     * @return string 
     */
    protected function getProductImageUrl($product){
        $imageUrl = $this->_store->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA) . 'catalog/product' . $product->getImage();
        return $imageUrl;
    }
    
    /**
     * Get product name
     * @param Magento\Catalog\Model\Product
     * @return string 
     */
    private function getProductName($product){
        $productName = $this->_outputHelper->productAttribute($product,
                              $product->getName(), 'name');

        return $productName;
    }

    /**
     * Get Catalog Block Image
     * @param Magento\Catalog\Model\Product
     * @return Magento\Catalog\Block\Product\Image
     */
    private function getBlockImage($product){
        $blockImage = $this->_blockImageFactory->create($product,
                      'category_page_grid');

        return $blockImage;
    }

    /**
     * Get product price
     * @param Magento\Catalog\Model\Product
     * @return string
     */
    private function getPrice($product){
        $price = $this->_priceRender->render(
            FinalPrice::PRICE_CODE,
            $product,
            [
                'include_container' => true,
                'display_minimal_price' => true,
                'zone' => Render::ZONE_ITEM_LIST,
                'list_category_page' => true
            ]
        );

        return $price;
    }

    static public function setStaticData($data){
        self::$_staticData['for_random_3_category_id'] = $data;
    }

    static public function getStaticData(){
        return isset(self::$_staticData['for_random_3_category_id']) ?self::$_staticData['for_random_3_category_id'] :null;
    }

    public function getCategoryId(){
        return $this->_categoryId;
    }

    public function setCategoryId($categoryId){
        $this->_categoryId = $categoryId;
    }
}