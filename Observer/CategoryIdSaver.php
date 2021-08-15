<?php

namespace Talexan\Random3\Observer;

use Magento\Framework\Event\ObserverInterface;
use Talexan\Random3\Model\Session as MySession;
use Talexan\Random3\CustomerData\CustomSidebar;

class CategoryIdSaver implements ObserverInterface
{
    /**
     * @var MySession $_mySession
     */
    protected $_mySession;

    /**
     * @var CustomSidebar
     */
    protected $_customSidebar;

    public function __construct(MySession $mySession,
    CustomSidebar $customSidebar)
  {
    // Observer initialization code...
    // You can use dependency injection to get any class this observer may need.

        $this->_mySession = $mySession;
        $this->_customSidebar = $customSidebar;
  }

  public function execute(\Magento\Framework\Event\Observer $observer)
  {
    $category = $observer->getData('category');
    // Additional observer execution code...

    if (isset($category)){
        $this->_mySession->setForRandom3CategoryId($category->getId());
        
        // For testing
        $this->_customSidebar->setCategoryId($category->getId());
        \Talexan\Random3\CustomerData\CustomSidebar::setStaticData($category->getId());
    }
  }
}
