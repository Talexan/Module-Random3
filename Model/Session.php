<?php
namespace Talexan\Random3\Model;

/**
 * Session model
 */
class Session extends \Magento\Framework\Session\SessionManager
{
    /**
     * @inheritdoc
     *
     * @return $this
     */
    public function clearStorage()
    {
        $categoryId = $this->getForRandom3CategoryId();
        
        parent::clearStorage();
       
        $this->setForRandom3CategoryId($categoryId);
        
        return $this;
    }
}