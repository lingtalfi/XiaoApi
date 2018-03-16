<?php


namespace XiaoApi\Observer;


interface ObserverInterface
{
    /**
     * @param $eventName
     * @param ... all subsequent params are also (the eventName is the first argument) passed as args to the listeners
     * @return mixed
     */
    public function trigger($eventName);

    /**
     * @param $eventName , string|array, the hook type(s) the listener wants to listen to
     * @param $listener , callable. It receives the eventName as first parameter, and the other args passed via the trigger method call
     */
    public function addListener($eventName, callable $listener);
}