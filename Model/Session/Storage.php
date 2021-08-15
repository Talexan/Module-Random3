<?php

namespace  Talexan\Random3\Model\Session;

class Storage extends \Magento\Framework\Session\Storage
{
    /**
     * Constructor
     *
     * @param string $namespace
     * @param array $data
     */
    public function __construct($namespace = 'talexan', array $data = [])
    {
        $this->namespace = $namespace;
        parent::__construct($namespace, $data);
    }
}
