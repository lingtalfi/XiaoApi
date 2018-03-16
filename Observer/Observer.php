<?php


namespace XiaoApi\Observer;


/**
 * Note: this is a singleton, so that we can be available from anywhere.
 *
 * I expect to be used from two different locations:
 *
 * - from objects themselves, which is convenient for small db fixes (like for instance if you need to handle conflict
 * of the default_image column on a table, which only ONE row should have...)
 * - from external parts, like for instance a cache system who wants to listen to certain events (or we could also
 * use the objects, but anyway the more flexible the better right?)
 *
 */
class Observer implements ObserverInterface
{

    private static $inst;
    private $listeners;

    private function __construct()
    {
        $this->listeners = [];
    }

    public static function inst()
    {
        if (null === self::$inst) {
            self::$inst = new self();
        }
        return self::$inst;
    }

    public function trigger($eventName)
    {
        $args = func_get_args();
        if (array_key_exists($eventName, $this->listeners)) {
            foreach ($this->listeners[$eventName] as $listener) {
                call_user_func_array($listener, $args);
            }
        }
    }

    public function addListener($eventName, callable $listener)
    {
        if (is_array($eventName)) {
            foreach ($eventName as $type) {
                $this->listeners[$type][] = $listener;
            }
        } else {
            $this->listeners[$eventName][] = $listener;
        }
    }
}