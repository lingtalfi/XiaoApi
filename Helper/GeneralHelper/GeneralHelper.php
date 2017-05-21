<?php


namespace XiaoApi\Helper\GeneralHelper;


class GeneralHelper
{

    public static function tableNameToClassName($table, $tablePrefix)
    {
        if (null !== $tablePrefix) {
            $table = str_replace($tablePrefix, '', $table);
        }
        $p = explode('_', $table);
        return implode('', array_map(function ($v) {
            return ucfirst($v);
        }, $p));
    }

}