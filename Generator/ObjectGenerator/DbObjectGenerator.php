<?php


namespace XiaoApi\Generator\ObjectGenerator;

use ArrayToString\ArrayToStringTool;
use Bat\FileSystemTool;
use QuickPdo\QuickPdoInfoTool;
use XiaoApi\Generator\ObjectGenerator\Exception\ObjectGeneratorException;
use XiaoApi\Helper\GeneralHelper\GeneralHelper;

class DbObjectGenerator
{

    private $namespace;
    private $targetDirectory;
    private $tablePrefix;


    public static function create()
    {
        return new static();
    }


    public function setNamespace($namespace)
    {
        $this->namespace = $namespace;
        return $this;
    }

    public function setTargetDirectory($targetDirectory)
    {
        $this->targetDirectory = $targetDirectory;
        return $this;
    }

    public function setTablePrefix($tablePrefix)
    {
        $this->tablePrefix = $tablePrefix;
        return $this;
    }


    public function generateByDatabase($db)
    {

        if (null === $this->targetDirectory) {
            throw new ObjectGeneratorException("targetDirectory not set");
        }

        $tables = QuickPdoInfoTool::getTables($db);
        $f = file_get_contents(__DIR__ . "/assets/GeneratedExampleObject.tpl.php");


        $s = '';
        foreach ($tables as $table) {


            //--------------------------------------------
            // GENERATING GENERATED CLASSES
            //--------------------------------------------
            $ClassName = GeneralHelper::tableNameToClassName($table, $this->tablePrefix);

            $fullTable = $db . "." . $table;

            $nullables = QuickPdoInfoTool::getColumnNullabilities($fullTable);
            $types = QuickPdoInfoTool::getColumnDataTypes($fullTable);
            $ai = QuickPdoInfoTool::getAutoIncrementedField($table, $db);
            $nf = [];
            $sDefaults = '';
            $dPrefix = "\t\t\t";
            foreach ($types as $column => $type) {
                if ($ai === $column) {
                    continue;
                }

                if (true === $nullables[$column]) {
                    $sDefaults .= $dPrefix . "'$column' => null," . PHP_EOL;
                    $nf[] = $column;
                } else {
                    switch ($type) {
                        case 'int':
                        case 'tinyint':
                            $sDefaults .= $dPrefix . "'$column' => 0," . PHP_EOL;
                            break;
                        default:
                            $sDefaults .= $dPrefix . "'$column' => ''," . PHP_EOL;
                            break;
                    }
                }
            }


            $s = '';
            foreach ($nf as $field) {

                $s .= <<<EEE
        if (0 === (int)\$ret["$field"]) {
            \$ret["$field"] = null;
        }
EEE;
                $s .= PHP_EOL;
            }


            $theClassName = "Generated" . $ClassName;
            $sArr = '[' . PHP_EOL . $sDefaults . "\t\t" . ']';

            $content = str_replace([
                'Module\Example\Api',
                'GeneratedExampleObject',
                'fullTable',
                '$array',
                '//-nullables',
            ], [
                $this->namespace,
                $theClassName,
                $fullTable,
                $sArr,
                $s,
            ], $f);


            $path = $this->targetDirectory . "/GeneratedObject/" . $theClassName . '.php';
            FileSystemTool::mkfile($path, $content);

            //--------------------------------------------
            // ALSO GENERATING MANUAL CLASSES IF NOT EXIST
            //--------------------------------------------
            $path = $this->targetDirectory . "/Object/" . $ClassName . '.php';
            if (false === file_exists($path)) {
                $c = file_get_contents(__DIR__ . "/assets/ManualObject.tpl.php");
                $c = str_replace([
                    'Module\Example\Api',
                    'GeneratedClassName',
                    'ClassName',
                ], [
                    $this->namespace,
                    $theClassName,
                    $ClassName,
                ], $c);
                FileSystemTool::mkfile($path, $c);
            }


        }

    }


}