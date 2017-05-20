<?php


namespace XiaoApi\Object;


use XiaoApi\Helper\QuickPdoStmtHelper\QuickPdoStmtHelper;
use QuickPdo\QuickPdo;
use QuickPdo\QuickPdoStmtTool;


abstract class TableCrudObject extends CrudObject
{

    protected $table;


    abstract protected function getCreateData(array $data);


    public function create(array $data)
    {
        $data = $this->getCreateData($data);
        $lastInsertId = QuickPdo::insert($this->table, $data);
        $this->hook("createAfter", [$this->table, $lastInsertId, $data]);
        return $lastInsertId;
    }


    /**
     * IMPORTANT NOTE:
     *
     * This is NOT a secure method.
     * It is meant to be used by developers for internal requests.
     * You can totally do sql injection if you want to.
     *
     * ------------------
     *
     *
     *
     * @param $params , an array, or object that can be converted to an array, with the following properties:
     *
     * - fields: null|array
     *                  the fields to return.
     *                  If null, will return all fields.
     *                  If an array, it's an array of key/value pairs.
     *                  Each key/value pair can be one of:
     *                      - (int =>) field
     *                      - field => alias
     *
     *                  The value (from any of those key/value pairs) will be used
     *                  as a column to retrieve (from mysql/yourDbm's perspective).
     *
     *                  Important note: there is no security check,
     *                  so, don't use user data without filtering them.
     *
     *
     *
     *
     * - where: null|array
     *              a quickPdoWhere array (https://github.com/lingtalfi/Quickpdo#the-where-notation),
     *              or null if there is no search criteria.
     *
     * - order: null|array
     *              the sort order.
     *              If null, no particular sort will be used.
     *              If it's an array, it's an array of field => direction.
     *              The direction must be one of asc or desc, case insensitive.
     *
     *
     * - nipp: null|int
     * - page: null|int
     *
     * @return array
     */
    public function read($params = [])
    {

        $params = array_replace([
            "fields" => null,
            "where" => null,
            "order" => null,
            "nipp" => 20,
            "page" => 1,
        ], (array)$params);


        $markers = [];
        $q = "SELECT ";


        QuickPdoStmtHelper::addFields($q, $params['fields']);
        $q .= ' FROM ' . $this->table;
        if (null !== $params['where']) {
            QuickPdoStmtTool::addWhereSubStmt($params['where'], $q, $markers);
        }
        QuickPdoStmtHelper::addOrderAndPage($q, $params['order'], $params['page'], $params['nipp']);

        return QuickPdo::fetchAll($q, $markers);
    }


    /**
     * Same as read, but fetches ONE result instead of ALL the result (fetch instead of fetchAll)
     */
    public function readOne($params = [])
    {

        $params = array_replace([
            "fields" => null,
            "where" => null,
        ], (array)$params);


        $markers = [];
        $q = "SELECT ";


        QuickPdoStmtHelper::addFields($q, $params['fields']);
        $q .= ' FROM ' . $this->table;
        if (null !== $params['where']) {
            QuickPdoStmtTool::addWhereSubStmt($params['where'], $q, $markers);
        }
        return QuickPdo::fetch($q, $markers);
    }

    public function update(array $data, array $where)
    {
        $pdoWhere = QuickPdoStmtHelper::simpleWhereToPdoWhere($where);
        QuickPdo::update($this->table, $data, $pdoWhere);
        $this->hook("updateAfter", [$this->table, $data, $where]);
    }


    public function delete(array $where)
    {
        $pdoWhere = QuickPdoStmtHelper::simpleWhereToPdoWhere($where);
        QuickPdo::delete($this->table, $pdoWhere);
        $this->hook("deleteAfter", [$this->table, $where]);
    }


}