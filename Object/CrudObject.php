<?php


namespace XiaoApi\Object;


use XiaoApi\Observer\ObserverInterface;

class CrudObject
{

    /**
     * @var ObserverInterface
     */
    private $observer;

    public function __construct()
    {
        $this->observer = null;
    }

    public function setObserver(ObserverInterface $observer)
    {
        $this->observer = $observer;
        return $this;
    }

    public function hook($hookType, $data)
    {
        $this->observer->hook($hookType, $data);
    }
}