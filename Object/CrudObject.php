<?php


namespace XiaoApi\Object;


use XiaoApi\Observer\Observer;

class CrudObject
{
    public function __construct()
    {
        // I'm a crud object
    }

    public static function getInst()
    {
        return new static();
    }


    //--------------------------------------------
    //
    //--------------------------------------------
    public function trigger($eventName)
    {
        call_user_func_array([Observer::inst(), "trigger"], func_get_args());
        return $this;
    }

    public function addListener($eventName, callable $listener)
    {
        Observer::inst()->addListener($eventName, $listener);
        return $this;
    }

}