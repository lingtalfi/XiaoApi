<?php


namespace Module\Example\Api\GeneratedObject;


use XiaoApi\Object\TableCrudObject;

/**
 * This file was generated by the DbObjectGenerator.
 * You should not edit it manually, otherwise you
 * might loose your edits on the next update.
 *
 * You are supposed to extend this object.
 */
class GeneratedExampleObject extends TableCrudObject
{

    public function __construct()
    {
        parent::__construct();
        $this->table = "fullTable";
    }


    //--------------------------------------------
    //
    //--------------------------------------------
    protected function getCreateData(array $data)
    {
        $ret = array_replace($array, $data);


//-nullables
        return $ret;
    }


}