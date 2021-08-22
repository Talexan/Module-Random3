<?php
    namespace Talexan\Random3\Controller\Content;

    use Magento\Framework\App\Action\Action;
    use Magento\Framework\App\Action\Context;
    use Magento\Framework\Controller\Result\JsonFactory;
    use Talexan\Random3\Model\Random\Product\SidebarJson;
    
    /**
     * Index action.
     */
    class Sidebar extends Action
    {
        /**
         * @var JsonFactory
         */
        protected $resultJsonFactory;

        /**
         * @var SidebarJson
        */
        protected $_model;
    
        
        /**
         * @param Context $context
         * @param JsonFactory $resultJsonFactory
         */
        public function __construct(
            Context $context,
            JsonFactory $resultJsonFactory,
            SidebarJson $model
        ) {
            parent::__construct($context);
            $this->resultJsonFactory = $resultJsonFactory;
            $this->_model = $model;
        }
    
        /**
         * Return Json
         *
         * @return Magento\Framework\Controller\Result\Json
         */
        public function execute()
        {            
            $resultJson = $this->resultJsonFactory->create()
                           ->setData($this->_model->getJsonData());
            
            return $resultJson;
        }
    }